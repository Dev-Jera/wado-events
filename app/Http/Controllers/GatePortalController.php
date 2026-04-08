<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class GatePortalController extends Controller
{
    public function index(Request $request)
    {
        $this->ensureGateStaff($request);

        $eventId = (int) $request->integer('event_id');

        $events = Event::query()
            ->with(['ticketCategories', 'tickets'])
            ->orderBy('starts_at')
            ->get();

        $rows = $events->map(function (Event $event) {
            $allocated = (int) $event->ticketCategories->sum('ticket_count');
            $inventoryRemaining = (int) $event->ticketCategories->sum('tickets_remaining');

            $qrGenerated = (int) $event->tickets
                ->filter(fn ($ticket) => ! blank($ticket->qr_code_path))
                ->sum('quantity');

            $scanned = (int) $event->tickets
                ->filter(fn ($ticket) => $ticket->status === 'used' || $ticket->used_at)
                ->sum('quantity');

            $confirmed = (int) $event->tickets
                ->filter(fn ($ticket) => $ticket->status === 'confirmed')
                ->sum('quantity');

            $cancelled = (int) $event->tickets
                ->filter(fn ($ticket) => $ticket->status === 'cancelled')
                ->sum('quantity');

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

            $categoryRows = $event->ticketCategories->map(function ($category) use ($event) {
                $tickets = $event->tickets->where('ticket_category_id', $category->id);

                $issuedWithQr = (int) $tickets->filter(fn ($ticket) => ! blank($ticket->qr_code_path))->sum('quantity');
                $scanned = (int) $tickets->filter(fn ($ticket) => $ticket->status === 'used' || $ticket->used_at)->sum('quantity');

                return [
                    'category' => $category,
                    'allocated' => (int) $category->ticket_count,
                    'inventory_remaining' => (int) $category->tickets_remaining,
                    'issued_with_qr' => $issuedWithQr,
                    'scanned' => $scanned,
                    'at_gate_remaining' => max($issuedWithQr - $scanned, 0),
                ];
            })->values();
        }

        return view('pages.gate.portal', [
            'rows' => $rows,
            'events' => $events,
            'selectedEventId' => $eventId,
            'selected' => $selected,
            'categoryRows' => $categoryRows,
        ]);
    }

    protected function ensureGateStaff(Request $request): void
    {
        abort_unless($request->user()?->isGateStaff(), 403);
    }
}
