<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Support\StaticEventCatalog;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class EventController extends Controller
{
    public function index()
    {
        $events = Cache::remember('events:list', 120, function () {
            return Event::query()
                ->with(['category', 'ticketCategories', 'artists'])
                ->where('status', 'published')
                ->where(function ($q) {
                    $q->where(function ($sub) {
                        $sub->whereNotNull('ends_at')->where('ends_at', '>=', now());
                    })->orWhere(function ($sub) {
                        $sub->whereNull('ends_at')->whereDate('starts_at', '>=', now()->toDateString());
                    });
                })
                ->orderByRaw("CASE WHEN starts_at <= NOW() THEN 0 ELSE 1 END")
                ->orderBy('starts_at')
                ->get()
                ->map(function (Event $event) {
                    $event->category_label = $event->category?->name ?? 'Uncategorized';
                    $event->url = route('events.show', $event);

                    return $event;
                });
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
        $event = Cache::tags(["event:{$event->id}"])->remember(
            "event:{$event->id}:detail",
            600,
            fn () => Event::query()->with(['category', 'ticketCategories', 'artists'])->findOrFail($event->id)
        );

        if ($event->ticketCategories->isNotEmpty()) {
            $event->forceFill([
                'capacity'          => (int) $event->ticketCategories->sum('ticket_count'),
                'tickets_available' => (int) $event->ticketCategories->sum('tickets_remaining'),
                'ticket_price'      => (float) $event->ticketCategories->min('price'),
            ]);
        }

        return view('pages.events.show', [
            'event' => $event,
        ]);
    }
}
