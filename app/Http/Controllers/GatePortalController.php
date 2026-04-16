<?php

namespace App\Http\Controllers;

use App\Http\Requests\WalkInSaleRequest;
use App\Jobs\ExpirePendingPayment;
use App\Jobs\IssueTicketForPayment;
use App\Models\Event;
use App\Models\PaymentTransaction;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\User;
use App\Services\Payment\MarzePayService;
use App\Services\Payment\PaymentLifecycleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class GatePortalController extends Controller
{
    public function __construct(
        protected MarzePayService $marzePayService,
        protected PaymentLifecycleService $paymentLifecycleService
    ) {
    }

    public function index(Request $request)
    {
        $this->authorize('accessGatePortal', Event::class);

        $eventId = (int) $request->integer('event_id');

        $events = Event::query()
            ->with([
                'ticketCategories:id,event_id,name,price,ticket_count,tickets_remaining,sort_order',
            ])
            ->withSum('ticketCategories as allocated_total', 'ticket_count')
            ->withSum('ticketCategories as inventory_remaining_total', 'tickets_remaining')
            ->withSum([
                'tickets as issued_with_qr_total' => fn ($q) => $q->whereNotNull('qr_code_path'),
            ], 'quantity')
            ->withSum([
                'tickets as scanned_total' => fn ($q) => $q->where(function ($sq): void {
                    $sq->where('status', Ticket::STATUS_USED)
                        ->orWhereNotNull('used_at');
                }),
            ], 'quantity')
            ->withSum([
                'tickets as confirmed_total' => fn ($q) => $q->where('status', Ticket::STATUS_CONFIRMED),
            ], 'quantity')
            ->withSum([
                'tickets as cancelled_total' => fn ($q) => $q->where('status', Ticket::STATUS_CANCELLED),
            ], 'quantity')
            ->orderBy('starts_at')
            ->get();

        $rows = $events->map(function (Event $event) {
            $allocated = (int) ($event->allocated_total ?? 0);
            $inventoryRemaining = (int) ($event->inventory_remaining_total ?? 0);
            $qrGenerated = (int) ($event->issued_with_qr_total ?? 0);
            $scanned = (int) ($event->scanned_total ?? 0);
            $confirmed = (int) ($event->confirmed_total ?? 0);
            $cancelled = (int) ($event->cancelled_total ?? 0);

            return [
                'event' => $event,
                'allocated' => $allocated,
                'inventory_remaining' => $inventoryRemaining,
                'issued_with_qr' => $qrGenerated,
                'scanned' => $scanned,
                'at_gate_remaining' => max($qrGenerated - $scanned, 0),
                'confirmed' => $confirmed,
                'cancelled' => $cancelled,
            ];
        });

        $selected = $rows->firstWhere('event.id', $eventId);

        $categoryRows = collect();
        if ($selected) {
            $event = $selected['event'];

            $categoryRows = TicketCategory::query()
                ->where('event_id', $event->id)
                ->withSum([
                    'tickets as issued_with_qr_total' => fn ($q) => $q->whereNotNull('qr_code_path'),
                ], 'quantity')
                ->withSum([
                    'tickets as scanned_total' => fn ($q) => $q->where(function ($sq): void {
                        $sq->where('status', Ticket::STATUS_USED)
                            ->orWhereNotNull('used_at');
                    }),
                ], 'quantity')
                ->orderBy('sort_order')
                ->get()
                ->map(function (TicketCategory $category): array {
                    $issuedWithQr = (int) ($category->issued_with_qr_total ?? 0);
                    $scanned = (int) ($category->scanned_total ?? 0);

                    return [
                        'category' => $category,
                        'allocated' => (int) $category->ticket_count,
                        'inventory_remaining' => (int) $category->tickets_remaining,
                        'issued_with_qr' => $issuedWithQr,
                        'scanned' => $scanned,
                        'at_gate_remaining' => max($issuedWithQr - $scanned, 0),
                    ];
                })
                ->values();
        }

        return view('pages.gate.portal', [
            'rows' => $rows,
            'events' => $events,
            'selectedEventId' => $eventId,
            'selected' => $selected,
            'categoryRows' => $categoryRows,
        ]);
    }

    public function storeWalkInSale(WalkInSaleRequest $request)
    {
        $this->authorize('accessGatePortal', Event::class);

        $data = $request->validated();

        $event = Event::query()->with('ticketCategories')->findOrFail((int) $data['event_id']);
        $ticketCategory = $event->ticketCategories->firstWhere('id', (int) $data['ticket_category_id']);
        abort_unless($ticketCategory instanceof TicketCategory, 404);

        $quantity = (int) $data['quantity'];
        $channel = strtolower(trim((string) $data['payment_channel']));
        $phone = trim((string) ($data['phone_number'] ?? ''));
        $isCashLike = in_array($channel, ['cash', 'pos'], true);

        if ($isCashLike && blank($data['collector_reference'] ?? null)) {
            throw ValidationException::withMessages([
                'collector_reference' => 'Receipt/reference is required for cash and POS walk-in payments.',
            ]);
        }

        $maxCashTicketsPerSale = (int) config('walkin.cash_max_tickets_per_sale', 6);
        if ($isCashLike && $quantity > $maxCashTicketsPerSale) {
            throw ValidationException::withMessages([
                'quantity' => 'Cash/POS sale exceeds allowed per-sale limit of ' . $maxCashTicketsPerSale . ' tickets.',
            ]);
        }

        if (in_array($channel, ['mtn', 'airtel'], true) && $phone === '') {
            throw ValidationException::withMessages([
                'phone_number' => 'Phone number is required for mobile money walk-in payment.',
            ]);
        }

        if ($isCashLike) {
            $dailyLimit = (float) config('walkin.cash_daily_limit_ugx', 0);
            $currentTotal = $this->getAgentCashCollectedToday((int) $request->user()->id);
            $incomingAmount = (float) $ticketCategory->price * $quantity;

            if ($dailyLimit > 0 && ($currentTotal + $incomingAmount) > $dailyLimit) {
                throw ValidationException::withMessages([
                    'payment_channel' => 'Cash/POS daily collection limit reached for this agent. Contact admin supervisor.',
                ]);
            }
        }

        $customer = $this->findOrCreateWalkInCustomer(
            trim((string) $data['holder_name']),
            (string) ($data['email'] ?? ''),
            $phone
        );

        $payment = DB::transaction(function () use ($data, $event, $ticketCategory, $quantity, $channel, $customer, $phone, $request) {
            $lockedCategory = TicketCategory::query()->lockForUpdate()->findOrFail($ticketCategory->id);
            $lockedEvent = Event::query()->lockForUpdate()->findOrFail($event->id);

            if ($lockedCategory->event_id !== $lockedEvent->id) {
                throw ValidationException::withMessages([
                    'ticket_category_id' => 'Ticket category does not belong to the selected event.',
                ]);
            }

            if ($lockedCategory->tickets_remaining < $quantity) {
                throw ValidationException::withMessages([
                    'quantity' => 'Not enough tickets remain for this category.',
                ]);
            }

            $lockedCategory->decrement('tickets_remaining', $quantity);
            $this->syncEventInventory($lockedEvent);

            $unitPrice = (float) $lockedCategory->price;
            $isInstantChannel = in_array($channel, ['cash', 'pos'], true);
            $idempotency = strtoupper((string) Str::uuid());

            return PaymentTransaction::query()->create([
                'user_id' => $customer->id,
                'event_id' => $lockedEvent->id,
                'ticket_category_id' => $lockedCategory->id,
                'holder_name' => trim((string) $data['holder_name']),
                'idempotency_key' => $idempotency,
                'payment_provider' => $channel,
                'sales_channel' => 'WALK_IN',
                'collected_by_user_id' => $request->user()?->id,
                'collected_at' => $isInstantChannel ? now() : null,
                'collector_reference' => trim((string) ($data['collector_reference'] ?? '')) ?: null,
                'phone_number' => $phone !== '' ? $phone : null,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total_amount' => $unitPrice * $quantity,
                'currency' => 'UGX',
                'status' => $isInstantChannel ? PaymentTransaction::STATUS_CONFIRMED : PaymentTransaction::STATUS_INITIATED,
                'confirmed_at' => $isInstantChannel ? now() : null,
                'expires_at' => $isInstantChannel ? null : now()->addMinutes(5),
            ]);
        });

        if (in_array($channel, ['cash', 'pos'], true)) {
            IssueTicketForPayment::dispatch($payment->id);

            return redirect()
                ->route('gate.portal', ['event_id' => $event->id])
                ->with('success', 'Walk-in payment recorded and ticket issuance queued.');
        }

        $initiation = $this->marzePayService->initiateStkPush($payment);
        if (! ($initiation['ok'] ?? false)) {
            $this->paymentLifecycleService->markFailedAndRelease(
                $payment,
                (string) ($initiation['message'] ?? 'Failed to initiate walk-in mobile money payment.')
            );

            return redirect()
                ->route('gate.portal', ['event_id' => $event->id])
                ->with('error', (string) ($initiation['message'] ?? 'Failed to initiate walk-in mobile money payment.'));
        }

        $payment->forceFill([
            'status' => PaymentTransaction::STATUS_PENDING,
            'provider_reference' => $initiation['reference'] ?: null,
            'provider_status' => $initiation['provider_status'] ?: null,
            'provider_payload' => $initiation['payload'],
            'last_error' => null,
        ])->save();

        ExpirePendingPayment::dispatch($payment->id)->delay(now()->addMinutes(5));

        return redirect()
            ->route('gate.portal', ['event_id' => $event->id])
            ->with('success', 'Walk-in mobile money request sent. Ask customer to approve PIN prompt.');
    }

    protected function findOrCreateWalkInCustomer(string $name, string $email, string $phone): User
    {
        $normalizedEmail = strtolower(trim($email));
        if ($normalizedEmail !== '') {
            $existingByEmail = User::query()->where('email', $normalizedEmail)->first();
            if ($existingByEmail) {
                if ($phone !== '' && blank($existingByEmail->phone)) {
                    $existingByEmail->forceFill(['phone' => $phone])->save();
                }

                return $existingByEmail;
            }
        }

        $normalizedPhone = preg_replace('/\D+/', '', $phone) ?: '';
        if ($normalizedPhone !== '') {
            $existingByPhone = User::query()
                ->where('phone', 'like', '%' . substr($normalizedPhone, -9))
                ->where('email', 'like', 'walkin+%@wado.local')
                ->first();

            if ($existingByPhone) {
                $existingByPhone->forceFill(['name' => $name])->save();

                return $existingByPhone;
            }
        }

        $resolvedEmail = $normalizedEmail !== ''
            ? $normalizedEmail
            : 'walkin+' . now()->format('YmdHis') . '-' . Str::lower(Str::random(6)) . '@wado.local';

        return User::query()->create([
            'name' => $name,
            'email' => $resolvedEmail,
            'phone' => $phone !== '' ? $phone : null,
            'password' => Str::random(40),
            'role' => 'customer',
        ]);
    }

    protected function syncEventInventory(Event $event): void
    {
        $inventory = TicketCategory::query()
            ->where('event_id', $event->id)
            ->selectRaw('COALESCE(SUM(tickets_remaining), 0) AS remaining_total')
            ->first();

        $event->forceFill([
            'tickets_available' => (int) ($inventory?->remaining_total ?? 0),
        ])->save();
    }

    protected function getAgentCashCollectedToday(int $userId): float
    {
        return (float) PaymentTransaction::query()
            ->where('sales_channel', 'WALK_IN')
            ->whereIn('payment_provider', ['cash', 'pos'])
            ->where('status', PaymentTransaction::STATUS_CONFIRMED)
            ->where('collected_by_user_id', $userId)
            ->whereDate('collected_at', now()->toDateString())
            ->sum('total_amount');
    }

}
