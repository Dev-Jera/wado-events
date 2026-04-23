<?php

namespace App\Http\Controllers;

use App\Http\Requests\TicketVerifyRequest;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\TicketScanLog;
use App\Services\Admin\AdminIncidentNotificationService;
use App\Services\Ticket\TicketQrService;
use App\Events\TicketScanned;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * @group Ticket Verification
 *
 * Scan and verify tickets at the event entrance.
 */
class TicketVerificationController extends Controller
{
    public function __construct(
        protected TicketQrService $ticketQrService,
        protected AdminIncidentNotificationService $incidentNotifications
    )
    {
    }

    public function index(Request $request)
    {
        $this->authorize('verify', Ticket::class);

        $user = $request->user();

        $events = $this->accessibleEventsQuery($user)
            ->orderBy('starts_at')
            ->get(['id', 'title', 'starts_at']);

        $selectedEventId = (int) $request->integer('event_id');

        if ($selectedEventId > 0 && ! $events->contains('id', $selectedEventId)) {
            return redirect()
                ->route('tickets.verify.index')
                ->with('error', 'You are not assigned to that event.');
        }

        $verificationRows = collect();
        if ($selectedEventId > 0) {
            $tickets = Ticket::query()
                ->with(['user', 'paymentTransaction'])
                ->where('event_id', $selectedEventId)
                ->orderByDesc('purchased_at')
                ->limit(300)
                ->get();

            $latestScanByTicketId = TicketScanLog::query()
                ->whereIn('ticket_id', $tickets->pluck('id')->all())
                ->orderByDesc('scanned_at')
                ->get()
                ->groupBy('ticket_id')
                ->map(fn ($rows) => $rows->first());

            $verificationRows = $tickets->map(function (Ticket $ticket) use ($latestScanByTicketId) {
                $generatedPayload = $this->ticketQrService->buildSignedPayload($ticket);
                $generatedSignatureOk = $this->ticketQrService->verifyPayloadSignature($generatedPayload);

                $latestScan = $latestScanByTicketId->get($ticket->id);
                $latestParsedPayload = $latestScan?->scanned_payload
                    ? $this->ticketQrService->parsePayload((string) $latestScan->scanned_payload)
                    : null;
                $latestSignatureOk = is_array($latestParsedPayload)
                    ? $this->ticketQrService->verifyPayloadSignature($latestParsedPayload)
                    : null;

                return [
                    'ticket' => $ticket,
                    'generated_payload_json' => json_encode($generatedPayload, JSON_UNESCAPED_SLASHES),
                    'generated_signature_ok' => $generatedSignatureOk,
                    'latest_scan' => $latestScan,
                    'latest_signature_ok' => $latestSignatureOk,
                ];
            });
        }

        return view('pages.tickets.verify', [
            'events' => $events,
            'selectedEventId' => $selectedEventId,
            'verificationRows' => $verificationRows,
        ]);
    }

    public function scanJson(Request $request): \Illuminate\Http\JsonResponse
    {
        $this->authorize('verify', Ticket::class);

        $data = $request->validate([
            'selected_event_id' => ['required', 'integer'],
            'ticket_code'       => ['nullable', 'string', 'max:120'],
            'scanned_payload'   => ['nullable', 'string', 'max:2000'],
            'device_id'         => ['nullable', 'string', 'max:120'],
        ]);

        $selectedEventId = (int) $data['selected_event_id'];

        if (! $request->user()?->canAccessGateEvent($selectedEventId)) {
            return response()->json(['ok' => false, 'message' => 'Not assigned to this event.'], 403);
        }

        $rawPayload  = trim((string) ($data['scanned_payload'] ?? ''));
        $payload     = $rawPayload !== '' ? $this->ticketQrService->parsePayload($rawPayload) : null;

        // Signature check
        if ($payload && ! $this->ticketQrService->verifyPayloadSignature($payload)) {
            $this->logScan($request, null, strtoupper((string) ($payload['code'] ?? ($data['ticket_code'] ?? ''))), 'fake', 'Invalid QR signature', $rawPayload, $selectedEventId);
            return response()->json(['ok' => false, 'reason' => 'fake', 'message' => 'Fake ticket.']);
        }

        // Event mismatch in payload
        if ($payload && isset($payload['event_id']) && (int) $payload['event_id'] !== $selectedEventId) {
            $this->logScan($request, null, strtoupper((string) ($payload['code'] ?? ($data['ticket_code'] ?? ''))), 'rejected', 'QR belongs to another event', $rawPayload, $selectedEventId);
            return response()->json(['ok' => false, 'reason' => 'wrong_event', 'message' => 'Does not belong to this event.']);
        }

        $ticketCode = $this->normalizeTicketCode((string) ($payload['code'] ?? ($data['ticket_code'] ?? '')));

        if ($ticketCode === '') {
            return response()->json(['ok' => false, 'message' => 'No ticket code found. Try scanning again.']);
        }

        // Find ticket
        $ticket = Ticket::query()->with(['event', 'user', 'ticketCategory'])->where('ticket_code', $ticketCode)->first();

        if (! $ticket) {
            $like   = '%' . str_replace('-', '%', $ticketCode) . '%';
            $ticket = Ticket::query()
                ->with(['event', 'user', 'ticketCategory'])
                ->where('event_id', $selectedEventId)
                ->where('ticket_code', 'like', $like)
                ->limit(50)->get()
                ->first(fn (Ticket $t) => $this->normalizeTicketCode((string) $t->ticket_code) === $ticketCode);
        }

        if (! $ticket) {
            $this->logScan($request, null, $ticketCode, 'rejected', 'Ticket not found', $rawPayload, $selectedEventId);
            return response()->json([
                'ok' => false,
                'reason' => 'fake',
                'message' => 'Fake ticket.',
                'detail' => 'This code does not match any ticket in the system.',
            ]);
        }

        if ((int) $ticket->event_id !== $selectedEventId) {
            $this->logScan($request, $ticket, $ticketCode, 'rejected', 'Ticket belongs to another event', $rawPayload, $selectedEventId);
            return response()->json([
                'ok' => false,
                'reason' => 'wrong_event',
                'message' => 'Does not belong to this event.',
                'detail' => 'This ticket is for: ' . ($ticket->event?->title ?? 'another event'),
                'holder' => $ticket->holder_name ?: $ticket->user?->name ?: 'Guest',
            ]);
        }

        $holderName = $ticket->holder_name ?: $ticket->user?->name ?: 'Guest';

        if ($ticket->status === Ticket::STATUS_CANCELLED) {
            $this->logScan($request, $ticket, $ticketCode, 'rejected', 'Ticket is cancelled', $rawPayload, $selectedEventId);
            return response()->json([
                'ok' => false,
                'reason' => 'fake',
                'message' => 'Fake ticket.',
                'detail' => 'This ticket has been cancelled and is not valid for entry.',
                'holder' => $holderName,
            ]);
        }

        // Fast path: already used — skip acquiring the lock entirely
        if ($ticket->status === Ticket::STATUS_USED || $ticket->used_at) {
            $this->logScan($request, $ticket, $ticketCode, 'already-used', 'Ticket already used', $rawPayload, $selectedEventId);
            $usedAt = $ticket->used_at ? $ticket->used_at->format('d M H:i') : null;
            return response()->json([
                'ok' => false,
                'reason' => 'already_used',
                'message' => 'Ticket already used.',
                'detail' => $usedAt ? "Scanned in at {$usedAt}." : 'This ticket has already been used.',
                'holder' => $holderName,
            ]);
        }

        // Redis lock (10 s TTL) prevents two simultaneous scans of the same code from
        // both succeeding. The DB transaction + lockForUpdate inside catches any race
        // that slips through (e.g. when Redis is unavailable and the lock is skipped).
        try {
            $broadcastData = null;

            $jsonResponse = Cache::lock('scan:' . $ticketCode, 10)->block(5, function () use (
                $request, $ticketCode, $rawPayload, $selectedEventId, &$broadcastData
            ): \Illuminate\Http\JsonResponse {
                return DB::transaction(function () use (
                    $request, $ticketCode, $rawPayload, $selectedEventId, &$broadcastData
                ): \Illuminate\Http\JsonResponse {
                    $locked = Ticket::query()
                        ->with(['event', 'user', 'ticketCategory'])
                        ->where('ticket_code', $ticketCode)
                        ->lockForUpdate()
                        ->first();

                    if (! $locked) {
                        return response()->json(['ok' => false, 'reason' => 'fake', 'message' => 'Ticket not found.']);
                    }

                    $holderName = $locked->holder_name ?: $locked->user?->name ?: 'Guest';

                    if ($locked->status === Ticket::STATUS_USED || $locked->used_at) {
                        $this->logScan($request, $locked, $ticketCode, 'already-used', 'Ticket already used', $rawPayload, $selectedEventId);
                        $usedAt = $locked->used_at ? $locked->used_at->format('d M H:i') : null;
                        return response()->json([
                            'ok'     => false,
                            'reason' => 'already_used',
                            'message' => 'Ticket already used.',
                            'detail' => $usedAt ? "Scanned in at {$usedAt}." : 'This ticket has already been used.',
                            'holder' => $holderName,
                        ]);
                    }

                    $locked->forceFill(['status' => Ticket::STATUS_USED, 'used_at' => now()])->save();

                    $this->logScan($request, $locked, $ticketCode, 'valid', 'Ticket verified and marked used', $rawPayload, $selectedEventId);

                    $broadcastData = [
                        'holder'   => $holderName,
                        'category' => $locked->ticketCategory?->name ?? '',
                    ];

                    return response()->json([
                        'ok'       => true,
                        'reason'   => 'valid',
                        'message'  => 'Ticket verified for ' . ($locked->event?->title ?? 'this event') . '.',
                        'detail'   => $holderName . ($locked->ticketCategory?->name ? ' · ' . $locked->ticketCategory->name : ''),
                        'holder'   => $holderName,
                        'event'    => $locked->event?->title ?? '',
                        'category' => $locked->ticketCategory?->name ?? '',
                        'code'     => $locked->ticket_code,
                    ]);
                });
            });

            // Broadcast after the DB transaction commits so the event is never
            // pushed to the queue while the row lock is still held.
            if ($broadcastData !== null) {
                broadcast(new TicketScanned(
                    eventId:   $selectedEventId,
                    ticketCode: $ticketCode,
                    holder:    $broadcastData['holder'],
                    category:  $broadcastData['category'],
                    result:    'valid',
                    ok:        true,
                    staffName: $request->user()?->name ?? 'Staff',
                    scannedAt: now()->format('H:i:s'),
                    deviceId:  $request->string('device_id')->toString(),
                ));
            }

            return $jsonResponse;

        } catch (LockTimeoutException) {
            return response()->json([
                'ok'      => false,
                'message' => 'Scanner is busy. Wait a moment and try again.',
            ], 429);
        }
    }

    public function export(Request $request)
    {
        $this->authorize('verify', Ticket::class);

        $data = $request->validate([
            'event_id' => ['required', 'integer', 'exists:events,id'],
        ]);

        $selectedEventId = (int) $data['event_id'];

        if (! $request->user()?->canAccessGateEvent($selectedEventId)) {
            abort(403);
        }

        $event = Event::query()->findOrFail($selectedEventId);

        $rows = Ticket::query()
            ->with(['user', 'event'])
            ->where('event_id', $selectedEventId)
            ->where('status', Ticket::STATUS_CONFIRMED)
            ->whereNull('used_at')
            ->orderBy('event_id')
            ->get()
            ->map(function (Ticket $ticket) {
                return $this->ticketQrService->buildSignedPayload($ticket);
            })
            ->values()
            ->all();

        $slug = str($event->title)->slug('-');
        $fileName = "ticket-offline-export-event-{$selectedEventId}-{$slug}-" . now()->format('Ymd-His') . '.json';

        return response()->streamDownload(function () use ($rows): void {
            echo json_encode([
                'generated_at' => now()->toIso8601String(),
                'rows' => $rows,
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        }, $fileName, [
            'Content-Type' => 'application/json',
        ]);
    }

    public function verify(TicketVerifyRequest $request)
    {
        $this->authorize('verify', Ticket::class);

        $data = $request->validated();

        $selectedEventId = (int) $data['selected_event_id'];

        if (! $request->user()?->canAccessGateEvent($selectedEventId)) {
            return redirect()
                ->route('tickets.verify.index', ['event_id' => $selectedEventId])
                ->with('error', 'You are not assigned to that event.');
        }

        $eventIdForRedirect = ['event_id' => $selectedEventId];

        $lookup = trim((string) ($data['lookup'] ?? ''));
        if ($lookup !== '' && blank($data['ticket_code'] ?? null) && blank($data['scanned_payload'] ?? null)) {
            $matches = Ticket::query()
                ->with(['event', 'user', 'ticketCategory'])
                ->where('event_id', $selectedEventId)
                ->where(function ($q) use ($lookup): void {
                    $q->where('holder_name', 'like', '%' . $lookup . '%')
                        ->orWhereHas('user', function ($uq) use ($lookup): void {
                            $uq->where('name', 'like', '%' . $lookup . '%')
                                ->orWhere('phone', 'like', '%' . $lookup . '%')
                                ->orWhere('email', 'like', '%' . $lookup . '%');
                        });
                })
                ->latest('purchased_at')
                ->limit(20)
                ->get();

            return redirect()
                ->route('tickets.verify.index', $eventIdForRedirect)
                ->withInput()
                ->with('lookup_results', $matches);
        }

        $rawPayload = trim((string) ($data['scanned_payload'] ?? ''));
        $payload = $rawPayload !== '' ? $this->ticketQrService->parsePayload($rawPayload) : null;

        if ($payload && ! $this->ticketQrService->verifyPayloadSignature($payload)) {
            $this->logScan($request, null, strtoupper((string) ($payload['code'] ?? ($data['ticket_code'] ?? ''))), 'fake', 'Invalid QR signature', $rawPayload, $selectedEventId);

            return redirect()
                ->route('tickets.verify.index', $eventIdForRedirect)
                ->withInput()
                ->with('verification', [
                    'ok' => false,
                    'message' => 'QR signature is invalid. Possible fake ticket.',
                    'payload' => $payload,
                ]);
        }

        if ($payload && isset($payload['event_id']) && (int) $payload['event_id'] !== $selectedEventId) {
            $this->logScan($request, null, strtoupper((string) ($payload['code'] ?? ($data['ticket_code'] ?? ''))), 'rejected', 'QR belongs to another event', $rawPayload, $selectedEventId);

            return redirect()
                ->route('tickets.verify.index', $eventIdForRedirect)
                ->withInput()
                ->with('verification', [
                    'ok' => false,
                    'message' => 'This QR belongs to a different event. Access denied.',
                    'payload' => $payload,
                ]);
        }

        $ticketCode = $this->normalizeTicketCode((string) ($payload['code'] ?? ($data['ticket_code'] ?? '')));

        if ($ticketCode === '') {
            return redirect()
                ->route('tickets.verify.index', $eventIdForRedirect)
                ->withInput()
                ->with('verification', [
                    'ok' => false,
                    'message' => 'No ticket code detected. Scan again or enter the code manually.',
                ]);
        }

        $ticket = Ticket::query()
            ->with(['event', 'user', 'ticketCategory'])
            ->where('ticket_code', $ticketCode)
            ->first();

        if (! $ticket && $ticketCode !== '') {
            $ticketCodeLike = '%' . str_replace('-', '%', $ticketCode) . '%';

            $ticket = Ticket::query()
                ->with(['event', 'user', 'ticketCategory'])
                ->where('event_id', $selectedEventId)
                ->where('ticket_code', 'like', $ticketCodeLike)
                ->limit(50)
                ->get()
                ->first(function (Ticket $candidate) use ($ticketCode): bool {
                    return $this->normalizeTicketCode((string) $candidate->ticket_code) === $ticketCode;
                });
        }

        if (! $ticket) {
            $this->logScan($request, null, $ticketCode, 'rejected', 'Ticket not found', $rawPayload, $selectedEventId);

            return redirect()
                ->route('tickets.verify.index', $eventIdForRedirect)
                ->withInput()
                ->with('verification', [
                    'ok' => false,
                    'message' => 'Ticket not found. Access denied.',
                    'payload' => $payload,
                ]);
        }

        if ((int) $ticket->event_id !== $selectedEventId) {
            $this->logScan($request, $ticket, $ticketCode, 'rejected', 'Ticket belongs to another event', $rawPayload, $selectedEventId);

            return redirect()
                ->route('tickets.verify.index', $eventIdForRedirect)
                ->withInput()
                ->with('verification', [
                    'ok' => false,
                    'message' => 'Ticket belongs to a different event. Access denied.',
                    'ticket' => $ticket,
                    'payload' => $payload,
                ]);
        }

        if ($ticket->status === Ticket::STATUS_CANCELLED) {
            $this->logScan($request, $ticket, $ticketCode, 'rejected', 'Ticket is cancelled', $rawPayload, $selectedEventId);

            return redirect()
                ->route('tickets.verify.index', $eventIdForRedirect)
                ->withInput()
                ->with('verification', [
                    'ok' => false,
                    'message' => 'Ticket is cancelled. Access denied.',
                    'ticket' => $ticket,
                    'payload' => $payload,
                ]);
        }

        if ($ticket->status === Ticket::STATUS_USED || $ticket->used_at) {
            $this->logScan($request, $ticket, $ticketCode, 'already-used', 'Ticket already used', $rawPayload, $selectedEventId);

            return redirect()
                ->route('tickets.verify.index', $eventIdForRedirect)
                ->withInput()
                ->with('verification', [
                    'ok' => false,
                    'message' => 'Ticket already used. Access denied.',
                    'ticket' => $ticket,
                    'payload' => $payload,
                ]);
        }

        try {
            [$markedTicket, $alreadyUsed] = Cache::lock('scan:' . $ticketCode, 10)->block(5, function () use (
                $ticket, $ticketCode
            ): array {
                return DB::transaction(function () use ($ticket, $ticketCode): array {
                    $locked = Ticket::query()
                        ->where('ticket_code', $ticketCode)
                        ->lockForUpdate()
                        ->first();

                    if (! $locked || $locked->status === Ticket::STATUS_USED || $locked->used_at) {
                        return [$locked, true];
                    }

                    $locked->forceFill(['status' => Ticket::STATUS_USED, 'used_at' => now()])->save();
                    return [$locked, false];
                });
            });
        } catch (LockTimeoutException) {
            return redirect()
                ->route('tickets.verify.index', $eventIdForRedirect)
                ->withInput()
                ->with('verification', [
                    'ok'      => false,
                    'message' => 'Scanner is busy. Wait a moment and try again.',
                    'ticket'  => $ticket,
                    'payload' => $payload,
                ]);
        }

        if ($alreadyUsed) {
            $this->logScan($request, $markedTicket ?? $ticket, $ticketCode, 'already-used', 'Ticket already used', $rawPayload, $selectedEventId);
            return redirect()
                ->route('tickets.verify.index', $eventIdForRedirect)
                ->withInput()
                ->with('verification', [
                    'ok'      => false,
                    'message' => 'Ticket already used. Access denied.',
                    'ticket'  => $markedTicket ?? $ticket,
                    'payload' => $payload,
                ]);
        }

        $this->logScan(
            $request,
            $markedTicket,
            $ticketCode,
            'valid',
            'Ticket verified and marked used',
            $rawPayload,
            $selectedEventId
        );

        return redirect()
            ->route('tickets.verify.index', $eventIdForRedirect)
            ->withInput()
            ->with('verification', [
                'ok'     => true,
                'message' => 'Ticket verified and marked as used.',
                'ticket'  => $markedTicket,
                'payload' => $payload,
            ]);
    }

    protected function logScan(
        Request $request,
        ?Ticket $ticket,
        string $ticketCode,
        string $result,
        string $message,
        ?string $rawPayload,
        ?int $selectedEventId = null
    ): void {
        $scanLog = TicketScanLog::query()->create([
            'ticket_id' => $ticket?->id,
            'staff_user_id' => $request->user()?->id,
            'ticket_code' => $ticketCode,
            'scanned_payload' => $rawPayload,
            'device_id' => $request->string('device_id')->toString() ?: null,
            'result' => $result,
            'message' => $selectedEventId ? ($message . " [gate_event_id={$selectedEventId}]") : $message,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'scanned_at' => now(),
        ]);

        if ($result !== 'valid') {
            $scanLog->loadMissing(['ticket.event', 'staff']);
            $this->incidentNotifications->notifyFailedTicketScan($scanLog, $selectedEventId);
        }
    }

    protected function normalizeTicketCode(string $value): string
    {
        $value = strtoupper(trim($value));
        $value = preg_replace('/[\x{2010}-\x{2015}\x{2212}]/u', '-', $value) ?? $value;
        $value = preg_replace('/\s+/', '', $value) ?? $value;

        return $value;
    }

    protected function accessibleEventsQuery(?\App\Models\User $user): Builder
    {
        $query = Event::query();

        if (! $user) {
            return $query->whereRaw('1 = 0');
        }

        if ($user->isSuperAdmin()) {
            return $query;
        }

        if ($user->isGateAgent()) {
            return $query->where(function (Builder $accessible) use ($user): void {
                $accessible->where('user_id', $user->id)
                    ->orWhereHas('gateAgents', fn (Builder $assigned) => $assigned->where('users.id', $user->id));
            });
        }

        return $query->whereRaw('1 = 0');
    }
}
