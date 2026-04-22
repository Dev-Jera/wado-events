<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Event;
use App\Support\StaticEventCatalog;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    public function __invoke()
    {
        $featuredEvents = Cache::remember('home:featured_events', 300, function () {
            return Event::query()
                ->with(['category', 'ticketCategories', 'artists'])
                ->orderByDesc('is_featured')
                ->orderBy('starts_at')
                ->get()
                ->map(function (Event $event) {
                    $event->category_label = $event->category?->name ?? 'Uncategorized';
                    $event->url = route('events.show', $event);

                    return $event;
                });
        });

        if ($featuredEvents->isEmpty()) {
            $featuredEvents = StaticEventCatalog::events();
        }

        $categoryPills = Cache::remember('home:category_pills', 3600, function () {
            return Category::query()
                ->withCount('events')
                ->orderBy('name')
                ->get()
                ->mapWithKeys(function (Category $category) {
                    return [
                        Str::lower($category->name) => [
                            'label' => $category->name,
                            'count' => (int) $category->events_count,
                        ],
                    ];
                });
        });

        return view('pages.home', [
            'featuredEvents' => $featuredEvents,
            'categoryPills' => $categoryPills,
        ]);
    }
}
