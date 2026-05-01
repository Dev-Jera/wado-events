<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Support\StaticEventCatalog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * @group Events
 *
 * Browse and view public events.
 */
class EventController extends Controller
{
    public function index(Request $request)
    {
        $category = Str::of((string) $request->query('category'))->lower()->trim()->toString();
        $search = Str::of((string) $request->query('search'))->lower()->trim()->toString();

        $events = Cache::remember('events:list:all-published', 120, function () {
            return Event::query()
                ->with(['category', 'ticketCategories', 'artists'])
                ->where('status', 'published')
                ->orderByDesc('starts_at')
                ->get()
                ->map(function (Event $event) {
                    $event->category_label = $event->category?->name ?? 'Uncategorized';
                    $event->url = route('events.show', $event);
                    $event->ticket_price = $event->ticketCategories->min('price') ?? $event->ticket_price;

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

        $categoryPills = $events
            ->groupBy(fn ($event) => Str::lower($event->category_label ?? 'uncategorized'))
            ->map(fn ($items, $key) => [
                'key' => $key,
                'label' => $items->first()->category_label ?? 'Uncategorized',
                'count' => $items->count(),
            ])
            ->sortBy('label')
            ->values();

        $filteredEvents = $events
            ->when($category !== '', fn ($items) => $items->filter(
                fn ($event) => Str::lower($event->category_label ?? 'uncategorized') === $category
            ))
            ->when($search !== '', fn ($items) => $items->filter(function ($event) use ($search) {
                return Str::of($event->title)->lower()->contains($search)
                    || Str::of($event->venue)->lower()->contains($search)
                    || Str::of($event->city)->lower()->contains($search)
                    || Str::of($event->description)->lower()->contains($search)
                    || Str::of($event->category_label)->lower()->contains($search)
                    || collect($event->artists ?? [])->contains(
                        fn ($artist) => Str::of($artist->name ?? '')->lower()->contains($search)
                    );
            }))
            ->sortBy(function ($event) {
                return match ($event->live_status) {
                    'live' => '0-' . $event->starts_at->timestamp,
                    'upcoming' => '1-' . $event->starts_at->timestamp,
                    default => '2-' . (PHP_INT_MAX - $event->starts_at->timestamp),
                };
            })
            ->values();

        $activeCategoryLabel = $categoryPills->firstWhere('key', $category)['label'] ?? null;

        return view('pages.events.index', [
            'events' => $filteredEvents,
            'bookmarkedIds' => $bookmarkedIds,
            'categoryPills' => $categoryPills,
            'activeCategory' => $category,
            'activeCategoryLabel' => $activeCategoryLabel,
            'search' => $search,
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
