<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Ticket;
use App\Models\TicketScanLog;
use App\Services\Ticket\TicketQrService;
use Illuminate\Http\Request;

class TicketVerificationController extends Controller
{
    public function __construct(protected TicketQrService $ticketQrService)
    {
    }

    public function index(Request $request)
    {
        $this->ensureAdmin($request);

        $events = Event::query()
            ->orderBy('starts_at')
            ->get(['id', 'title', 'starts_at']);

        $selectedEventId = (int) $request->integer('event_id');

        return view('pages.tickets.verify', [
            'events' => $events,
            'selectedEventId' => $selectedEventId,
        ]);
    }

    public function export(Request $request)
    {
        $this->ensureAdmin($request);

        $data = $request->validate([
            'event_id' => ['required', 'integer', 'exists:events,id'],
        ]);

        $selectedEventId = (int) $data['event_id'];
        $event = Event::query()->findOrFail($selectedEventId);

        $rows = Ticket::query()
            ->with(['user', 'event'])
            ->where('event_id', $selectedEventId)
            ->where('status', 'confirmed')
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

    public function verify(Request $request)
    {
        $this->ensureAdmin($request);

        $data = $request->validate([
            'selected_event_id' => ['required', 'integer', 'exists:events,id'],
            'ticket_code' => ['nullable', 'string'],
            'scanned_payload' => ['nullable', 'string'],
            'lookup' => ['nullable', 'string', 'max:255'],
            'mark_as_used' => ['nullable', 'boolean'],
            'device_id' => ['nullable', 'string', 'max:120'],
        ]);

        $selectedEventId = (int) $data['selected_event_id'];

        $eventIdForRedirect = ['event_id' => $selectedEventId];

        $lookup = trim((string) ($data['lookup'] ?? ''));
        if ($lookup !== '' && blank($data['ticket_code'] ?? null) && blank($data['scanned_payload'] ?? null)) {
            $matches = Ticket::query()
                ->with(['event', 'user', 'ticketCategory'])
                ->where('event_id', $selectedEventId)
                ->whereHas('user', function ($q) use ($lookup): void {
                    $q->where('name', 'like', '%' . $lookup . '%')
                        ->orWhere('phone', 'like', '%' . $lookup . '%')
                        ->orWhere('email', 'like', '%' . $lookup . '%');
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

        $ticketCode = strtoupper(trim((string) ($payload['code'] ?? ($data['ticket_code'] ?? ''))));

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

        if ($ticket->status === 'cancelled') {
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

        if ($ticket->status === 'used' || $ticket->used_at) {
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

        $markAsUsed = $request->boolean('mark_as_used', true);

        if ($markAsUsed) {
            $ticket->forceFill([
                'status' => 'used',
                'used_at' => now(),
            ])->save();
        }

        $this->logScan(
            $request,
            $ticket,
            $ticketCode,
            'valid',
            $markAsUsed ? 'Ticket verified and marked used' : 'Ticket verified',
            $rawPayload,
            $selectedEventId
        );

        return redirect()
            ->route('tickets.verify.index', $eventIdForRedirect)
            ->withInput()
            ->with('verification', [
                'ok' => true,
                'message' => $markAsUsed ? 'Ticket verified and marked as used.' : 'Ticket is valid.',
                'ticket' => $ticket,
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
        TicketScanLog::query()->create([
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
    }

    protected function ensureAdmin(Request $request): void
    {
        abort_unless($request->user()?->isGateStaff(), 403);
    }
}
