<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Support\StaticEventCatalog;
use Illuminate\Support\Collection;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::query()
            ->with(['category', 'ticketCategories', 'artists'])
            ->where('status', 'published')
            ->where(function ($q) {
                // Keep events that haven't ended yet
                $q->where(function ($sub) {
                    // Has an end time that hasn't passed
                    $sub->whereNotNull('ends_at')->where('ends_at', '>=', now());
                })->orWhere(function ($sub) {
                    // No end time: only show if starts today or in the future
                    $sub->whereNull('ends_at')->whereDate('starts_at', '>=', now()->toDateString());
                });
            })
            ->orderByRaw("CASE WHEN starts_at <= NOW() THEN 0 ELSE 1 END") // live first
            ->orderBy('starts_at')
            ->get()
            ->map(function (Event $event) {
                $event->category_label = $event->category?->name ?? 'Uncategorized';
                $event->url = route('events.show', $event);

                return $event;
            });

        if ($events->isEmpty()) {
            $events = StaticEventCatalog::events();
        }

        $bookmarkedIds = collect();
        if ($user = auth()->user()) {
            $bookmarkedIds = $user->bookmarks()
                ->whereIn('event_id', $events->pluck('id'))
                ->pluck('event_id');
        }

        return view('pages.events.index', [
            'events' => $events,
            'bookmarkedIds' => $bookmarkedIds,
        ]);
    }

    public function show(Event $event)
    {
        $event->load(['category', 'ticketCategories', 'artists']);

        if ($event->ticketCategories->isNotEmpty()) {
            $event->forceFill([
                'capacity' => (int) $event->ticketCategories->sum('ticket_count'),
                'tickets_available' => (int) $event->ticketCategories->sum('tickets_remaining'),
                'ticket_price' => (float) $event->ticketCategories->min('price'),
            ]);
        }

        return view('pages.events.show', [
            'event' => $event,
        ]);
    }
}
