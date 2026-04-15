<?php

namespace App\Http\Controllers;

use App\Jobs\ExpirePendingPayment;
use App\Models\Event;
use App\Models\PaymentTransaction;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\User;
use App\Services\Payment\MarzePayService;
use App\Services\Payment\PaymentLifecycleService;
use App\Services\Ticket\TicketQrService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    public function __construct(
        protected TicketQrService $ticketQrService,
        protected MarzePayService $marzePayService,
        protected PaymentLifecycleService $paymentLifecycleService
    ) {
    }

    public function create(Request $request, Event $event)
    {
        $event->load('ticketCategories');

        abort_if($event->ticketCategories->isEmpty(), 404);

        $prefill = collect($request->session()->pull("checkout_prefill.{$event->id}", []));
        $selectedCategoryId = (int) ($request->integer('ticket_category') ?: $prefill->get('ticket_category_id', 0));
        $selectedPaymentProvider = $request->string('payment_provider')->toString() ?: (string) $prefill->get('payment_provider', '');
        $selectedQuantity = (int) ($prefill->get('quantity', 1));
        $phoneNumber = (string) ($prefill->get('phone_number') ?: optional($request->user())->phone ?: '');
        $holderName = (string) ($prefill->get('holder_name') ?: optional($request->user())->name ?: '');
        $email = (string) ($prefill->get('email') ?: optional($request->user())->email ?: '');
        $createAccount = (bool) $prefill->get('create_account', false);

        $idempotencySessionKey = "checkout_idempotency.{$event->id}";
        $idempotencyKey = old('idempotency_key')
            ?: (string) $prefill->get('idempotency_key', '')
            ?: (string) $request->session()->get($idempotencySessionKey, '');

        if ($idempotencyKey === '') {
            $idempotencyKey = (string) Str::uuid();
            $request->session()->put($idempotencySessionKey, $idempotencyKey);
        }

        return view('pages.checkout.create', [
            'event' => $event,
            'selectedCategoryId' => $selectedCategoryId,
            'selectedPaymentProvider' => $selectedPaymentProvider,
            'selectedQuantity' => max($selectedQuantity, 1),
            'phoneNumber' => $phoneNumber,
            'holderName' => $holderName,
            'email' => $email,
            'createAccount' => $createAccount,
            'idempotencyKey' => $idempotencyKey,
        ]);
    }

    public function store(Request $request, Event $event)
    {
        $event->load('ticketCategories');
        $user = $request->user();

        $rateLimitKey = $this->purchaseRateLimitKey($request, $event);
        if (RateLimiter::tooManyAttempts($rateLimitKey, 10)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);

            throw ValidationException::withMessages([
                'quantity' => 'Too many purchase attempts. Try again in ' . max($seconds, 1) . ' seconds.',
            ]);
        }
        RateLimiter::hit($rateLimitKey, 600);

        $data = $request->validate([
            'ticket_category_id' => ['required', 'integer'],
            'quantity' => ['required', 'integer', 'min:1', 'max:20'],
            'holder_name' => ['required', 'string', 'max:120'],
            'email' => [$user ? 'nullable' : 'required', 'email', 'max:255'],
            'payment_provider' => ['nullable', 'in:mtn,airtel'],
            'phone_number' => ['nullable', 'string', 'max:40'],
            'create_account' => ['nullable', 'boolean'],
            'password' => ['nullable', 'string', 'min:8', 'required_if:create_account,1', 'confirmed'],
            'idempotency_key' => ['required', 'string', 'max:64'],
        ]);

        $createAccount = (bool) ($data['create_account'] ?? false);
        $resolved = $this->resolveCheckoutUser($request, $data, $createAccount);
        $user = $resolved['user'];
        $guestCheckout = (bool) ($resolved['guest_checkout'] ?? false);

        $ticketCategory = $event->ticketCategories
            ->firstWhere('id', (int) $data['ticket_category_id']);

        abort_unless($ticketCategory instanceof TicketCategory, 404);

        $quantity = (int) $data['quantity'];
        $unitPrice = (float) $ticketCategory->price;
        $isFree = $unitPrice <= 0;

        if (! $isFree && blank($data['payment_provider'] ?? null)) {
            return back()
                ->withInput()
                ->withErrors([
                    'payment_provider' => 'Choose MTN or Airtel to continue.',
                ]);
        }

        if (! $isFree && blank($data['phone_number'] ?? null)) {
            return back()
                ->withInput()
                ->withErrors([
                    'phone_number' => 'Phone number is required for mobile money payments.',
                ]);
        }

        if ($isFree) {
            $ticket = DB::transaction(function () use ($data, $event, $quantity, $ticketCategory, $unitPrice, $user) {
                $lockedCategory = TicketCategory::query()->lockForUpdate()->findOrFail($ticketCategory->id);
                $lockedEvent = Event::query()->lockForUpdate()->findOrFail($event->id);

                if ($lockedCategory->tickets_remaining < $quantity) {
                    throw ValidationException::withMessages([
                        'quantity' => 'Not enough tickets remain for that selection.',
                    ]);
                }

                $lockedCategory->decrement('tickets_remaining', $quantity);
                $this->syncEventInventory($lockedEvent);

                $ticket = Ticket::create([
                    'user_id' => $user->id,
                    'event_id' => $event->id,
                    'ticket_category_id' => $lockedCategory->id,
                    'holder_name' => trim($data['holder_name']),
                    'ticket_code' => $this->ticketQrService->generateUniqueTicketCode($user->id, $event->id),
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total_amount' => $unitPrice * $quantity,
                    'payment_provider' => 'free',
                    'status' => 'confirmed',
                    'purchased_at' => now(),
                ]);

                $ticket->forceFill([
                    'qr_code_path' => $this->ticketQrService->generateAndStoreForTicket($ticket),
                ])->save();

                return $ticket;
            });

            if ($guestCheckout) {
                return redirect()
                    ->route('events.show', $event)
                    ->with('success', 'Free ticket issued. We sent your ticket details to your email. Keep your payment phone active for SMS updates.');
            }

            return redirect()
                ->route('tickets.show', $ticket)
                ->with('success', 'Free ticket issued successfully.');
        }

        $idempotencyKey = strtoupper(trim((string) $data['idempotency_key']));
        $existingPayment = PaymentTransaction::query()
            ->where('idempotency_key', $idempotencyKey)
            ->where('user_id', $user->id)
            ->whereIn('status', [
                PaymentTransaction::STATUS_INITIATED,
                PaymentTransaction::STATUS_PENDING,
            ])
            ->first();

        if ($existingPayment) {
            return $this->redirectForExistingPayment($request, $existingPayment);
        }

        if (PaymentTransaction::query()
            ->where('idempotency_key', $idempotencyKey)
            ->where('user_id', $user->id)
            ->exists()
        ) {
            $idempotencyKey = strtoupper((string) Str::uuid());
        }

        $payment = DB::transaction(function () use ($data, $event, $idempotencyKey, $quantity, $ticketCategory, $unitPrice, $user) {
            $lockedCategory = TicketCategory::query()->lockForUpdate()->findOrFail($ticketCategory->id);
            $lockedEvent = Event::query()->lockForUpdate()->findOrFail($event->id);

            if ($lockedCategory->tickets_remaining < $quantity) {
                throw ValidationException::withMessages([
                    'quantity' => 'Not enough tickets remain for that selection.',
                ]);
            }

            $lockedCategory->decrement('tickets_remaining', $quantity);
            $this->syncEventInventory($lockedEvent);

            return PaymentTransaction::query()->create([
                'user_id' => $user->id,
                'event_id' => $event->id,
                'ticket_category_id' => $lockedCategory->id,
                'holder_name' => trim($data['holder_name']),
                'idempotency_key' => $idempotencyKey,
                'payment_provider' => $data['payment_provider'],
                'phone_number' => trim((string) $data['phone_number']),
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total_amount' => $unitPrice * $quantity,
                'currency' => 'UGX',
                'status' => PaymentTransaction::STATUS_INITIATED,
                'expires_at' => now()->addMinutes(5),
            ]);
        });

        \Illuminate\Support\Facades\Log::error('CheckoutController: initiating Marz Pay request for payment_id=' . $payment->id . ' provider=' . ($data['payment_provider'] ?? ''));
        $initiation = $this->marzePayService->initiateStkPush($payment);
        if (! ($initiation['ok'] ?? false)) {
            $this->paymentLifecycleService->markFailedAndRelease(
                $payment,
                (string) ($initiation['message'] ?? 'Failed to initiate payment.')
            );

            $this->storeCheckoutPrefill($request, $event, [
                'ticket_category_id' => $ticketCategory->id,
                'quantity' => $quantity,
                'holder_name' => trim($data['holder_name']),
                'email' => $data['email'] ?? null,
                'create_account' => $createAccount,
                'payment_provider' => $data['payment_provider'],
                'phone_number' => trim((string) $data['phone_number']),
                'idempotency_key' => $idempotencyKey,
            ]);

            return redirect()
                ->route('checkout.create', [
                    'event' => $event,
                    'ticket_category' => $ticketCategory->id,
                    'payment_provider' => $data['payment_provider'],
                ])
                ->with('payment_notice', [
                    'type' => 'error',
                    'message' => (string) ($initiation['message'] ?? 'Failed to initiate payment.'),
                ]);
        }

        $payment->forceFill([
            'status' => PaymentTransaction::STATUS_PENDING,
            'provider_reference' => $initiation['reference'] ?: null,
            'provider_status' => $initiation['provider_status'] ?: null,
            'provider_payload' => $initiation['payload'],
            'last_error' => null,
        ])->save();

        ExpirePendingPayment::dispatch($payment->id)->delay(now()->addMinutes(5));

        $providerLabel = $this->paymentProviderLabel((string) $data['payment_provider']);
        $phoneNumber = trim((string) $data['phone_number']);

        $this->storeCheckoutPrefill($request, $event, [
            'ticket_category_id' => $ticketCategory->id,
            'quantity' => $quantity,
            'holder_name' => trim($data['holder_name']),
            'email' => $data['email'] ?? null,
            'create_account' => $createAccount,
            'payment_provider' => $data['payment_provider'],
            'phone_number' => $phoneNumber,
            'idempotency_key' => $idempotencyKey,
        ]);

        return redirect()
            ->route('checkout.create', [
                'event' => $event,
                'ticket_category' => $ticketCategory->id,
                'payment_provider' => $data['payment_provider'],
            ])
            ->with('payment_notice', [
                'type' => 'success',
                'message' => "We have sent a payment request via {$providerLabel} to {$phoneNumber}. Check your phone and enter your PIN to complete payment.",
            ]);
    }

    protected function redirectForExistingPayment(Request $request, PaymentTransaction $payment)
    {
        if ($payment->ticket_id) {
            return redirect()
                ->route('tickets.show', $payment->ticket_id)
                ->with('success', 'Payment already confirmed. Showing your ticket.');
        }

        $event = Event::query()->find($payment->event_id);
        if (! $event) {
            return redirect()
                ->route('events.index')
                ->with('warning', 'Your payment request was found, but the event is unavailable.');
        }

        $this->storeCheckoutPrefill($request, $event, [
            'ticket_category_id' => $payment->ticket_category_id,
            'quantity' => $payment->quantity,
            'holder_name' => $payment->holder_name,
            'email' => $payment->user?->email,
            'create_account' => false,
            'payment_provider' => $payment->payment_provider,
            'phone_number' => $payment->phone_number,
            'idempotency_key' => $payment->idempotency_key,
        ]);

        $status = strtoupper((string) $payment->status);
        $noticeType = in_array($status, [PaymentTransaction::STATUS_FAILED, PaymentTransaction::STATUS_REFUNDED], true)
            ? 'error'
            : 'info';

        $noticeMessage = in_array($status, [PaymentTransaction::STATUS_PENDING, PaymentTransaction::STATUS_INITIATED], true)
            ? 'A payment request is already active. Check your phone and enter your PIN to complete payment.'
            : ((string) ($payment->last_error ?: 'This payment request already exists. Returning latest status.'));

        return redirect()
            ->route('checkout.create', [
                'event' => $event,
                'ticket_category' => $payment->ticket_category_id,
                'payment_provider' => $payment->payment_provider,
            ])
            ->with('payment_notice', [
                'type' => $noticeType,
                'message' => $noticeMessage,
            ]);
    }

    protected function syncEventInventory(Event $event): void
    {
        $inventory = TicketCategory::query()
            ->where('event_id', $event->id)
            ->selectRaw('COALESCE(SUM(ticket_count), 0) AS capacity_total, COALESCE(SUM(tickets_remaining), 0) AS remaining_total')
            ->first();

        $event->forceFill([
            'capacity' => (int) ($inventory?->capacity_total ?? 0),
            'tickets_available' => (int) ($inventory?->remaining_total ?? 0),
        ])->save();
    }

    protected function storeCheckoutPrefill(Request $request, Event $event, array $payload): void
    {
        $request->session()->put("checkout_prefill.{$event->id}", $payload);
    }

    protected function paymentProviderLabel(string $provider): string
    {
        return match (strtolower(trim($provider))) {
            'mtn' => 'MTN MoMo',
            'airtel' => 'Airtel Money',
            default => 'Mobile Money',
        };
    }

    protected function purchaseRateLimitKey(Request $request, Event $event): string
    {
        $userPart = $request->user()?->id ? ('user:' . $request->user()->id) : ('ip:' . $request->ip());

        return 'checkout:purchase:' . $event->id . ':' . $userPart;
    }

    /**
     * Resolve the customer used for checkout.
     * Guest checkout creates a customer record without forcing sign-in.
     * Optional account creation signs in the user immediately.
     *
     * @return array{user: User, guest_checkout: bool}
     */
    protected function resolveCheckoutUser(Request $request, array $data, bool $createAccount): array
    {
        if ($request->user()) {
            return [
                'user' => $request->user(),
                'guest_checkout' => false,
            ];
        }

        $email = strtolower(trim((string) ($data['email'] ?? '')));
        $holderName = trim((string) ($data['holder_name'] ?? ''));
        $phone = trim((string) ($data['phone_number'] ?? '')) ?: null;
        $sessionGuestUserId = (int) $request->session()->get('checkout_guest_user_id', 0);

        $existing = User::query()->where('email', $email)->first();
        if ($existing) {
            if ($sessionGuestUserId > 0 && $existing->id === $sessionGuestUserId) {
                $existing->forceFill([
                    'name' => $holderName,
                    'phone' => $phone,
                ])->save();

                if ($createAccount) {
                    $existing->forceFill([
                        'password' => (string) $data['password'],
                    ])->save();

                    Auth::login($existing);
                    $request->session()->regenerate();
                    $request->session()->forget('checkout_guest_user_id');

                    return [
                        'user' => $existing,
                        'guest_checkout' => false,
                    ];
                }

                return [
                    'user' => $existing,
                    'guest_checkout' => true,
                ];
            }

            throw ValidationException::withMessages([
                'email' => 'An account with this email already exists. Log in to track tickets or use a different email for guest checkout.',
            ]);
        }

        $user = User::query()->create([
            'name' => $holderName,
            'email' => $email,
            'phone' => $phone,
            'password' => $createAccount ? (string) $data['password'] : Str::random(40),
            'role' => 'customer',
        ]);

        if ($createAccount) {
            Auth::login($user);
            $request->session()->regenerate();
            $request->session()->forget('checkout_guest_user_id');
        } else {
            $request->session()->put('checkout_guest_user_id', $user->id);
        }

        return [
            'user' => $user,
            'guest_checkout' => ! $createAccount,
        ];
    }
}
