<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Support\StaticEventCatalog;

class HomeController extends Controller
{
    public function __invoke()
    {
        $featuredEvents = Event::query()
            ->with(['category', 'ticketCategories', 'artists'])
            ->orderByDesc('is_featured')
            ->orderBy('starts_at')
            ->limit(6)
            ->get()
            ->map(function (Event $event) {
                $event->category_label = $event->category?->name ?? 'Uncategorized';
                $event->url = route('events.show', $event);

                return $event;
            });

        if ($featuredEvents->isEmpty()) {
            $featuredEvents = StaticEventCatalog::events();
        }

        return view('pages.home', [
            'featuredEvents' => $featuredEvents,
        ]);
    }
}
