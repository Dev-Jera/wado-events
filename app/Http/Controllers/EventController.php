<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Support\StaticEventCatalog;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::query()
            ->with(['category', 'ticketCategories'])
            ->orderByRaw("CASE WHEN status = 'published' THEN 0 ELSE 1 END")
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

        return view('pages.events.index', [
            'events' => $events,
        ]);
    }

    public function show(Event $event)
    {
        $event->load(['category', 'ticketCategories']);

        return view('pages.events.show', [
            'event' => $event,
        ]);
    }
}
