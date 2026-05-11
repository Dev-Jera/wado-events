<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Support\StaticEventCatalog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
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
        if ($event->ticketCategories()->exists()) {
            return redirect()->route('checkout.create', $event);
        }

        return redirect()->route('events.index');
    }

    public function create()
    {
        $categories = \App\Models\Category::all();
        
        return view('pages.events.create', [
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'venue' => 'required|string|max:255',
            'city' => 'required|string|max:120',
            'country' => 'required|string|max:120',
            'starts_at' => 'required|date_format:Y-m-d\TH:i',
            'ends_at' => 'nullable|date_format:Y-m-d\TH:i|after:starts_at',
            'description' => 'required|string',
            'image_url' => 'nullable|image|max:5120',
            'verification_mode' => 'required|in:wado_managed,self_managed',
            'is_free' => 'boolean',
            'reentry_allowed' => 'boolean',
            'reentry_limit' => 'nullable|integer|min:1',
            'reentry_cooldown_minutes' => 'nullable|integer|min:0',
            'ticket_categories' => 'required|array|min:1',
            'ticket_categories.*.name' => 'required|string|max:100',
            'ticket_categories.*.price' => 'nullable|numeric|min:0',
            'ticket_categories.*.ticket_count' => 'required|integer|min:1',
            'ticket_categories.*.description' => 'nullable|string',
        ]);

        // Handle image upload
        if ($request->hasFile('image_url')) {
            $validated['image_url'] = $request->file('image_url')->store('event-images', 'public');
        } else {
            unset($validated['image_url']);
        }

        $user = auth()->user();

        $event = DB::transaction(function () use ($validated, $user) {
            // Create event with pending status (awaiting admin approval)
            $event = new \App\Models\Event();
            $event->fill($validated);
            $event->status = 'pending';
            $event->user_id = $user->id;
            $event->is_featured = false;
            $baseSlug = Str::slug($validated['title']);
            $slug = $baseSlug;
            $suffix = 1;
            while (Event::query()->where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $suffix;
                $suffix++;
            }
            $event->slug = $slug;
            $event->verification_mode = $validated['verification_mode'] ?? 'wado_managed';
            $event->reentry_allowed = $validated['reentry_allowed'] ?? false;
            $event->reentry_limit = $validated['reentry_limit'] ?? 1;
            $event->reentry_cooldown_minutes = $validated['reentry_cooldown_minutes'] ?? 0;
            $event->is_free = $validated['is_free'] ?? false;

            $ticketCategories = collect($validated['ticket_categories'] ?? []);
            $event->capacity = (int) $ticketCategories->sum(fn (array $ticketCat): int => (int) ($ticketCat['ticket_count'] ?? 0));
            $event->tickets_available = $event->capacity;

            if ($event->is_free) {
                $event->ticket_price = 0;
            } else {
                $lowestPrice = $ticketCategories
                    ->map(fn (array $ticketCat): float => (float) ($ticketCat['price'] ?? 0))
                    ->min();
                $event->ticket_price = $lowestPrice ?? 0;
            }

            $event->save();

            // Create ticket categories
            if (!empty($validated['ticket_categories'])) {
                foreach ($validated['ticket_categories'] as $index => $ticketCat) {
                    $price = $event->is_free ? 0 : ($ticketCat['price'] ?? 0);
                    $event->ticketCategories()->create([
                        'name' => $ticketCat['name'],
                        'price' => $price,
                        'ticket_count' => $ticketCat['ticket_count'],
                        'tickets_remaining' => $ticketCat['ticket_count'],
                        'description' => $ticketCat['description'] ?? null,
                        'sort_order' => $index,
                    ]);
                }
            }

            return $event;
        });

        // Send confirmation email via Brevo API (HTTP), but never block event submission on email failure.
        $event->loadMissing(['category', 'ticketCategories']);
        try {
            $apiKey = (string) config('services.brevo.api_key', '');

            if ($apiKey === '') {
                throw new \RuntimeException('Brevo API key not configured (BREVO_API_KEY)');
            }

            $html = view('emails.events.submitted', [
                'event' => $event,
                'user' => $user,
            ])->render();

            $response = Http::timeout(20)
                ->withHeaders([
                    'api-key' => $apiKey,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->post('https://api.brevo.com/v3/smtp/email', [
                    'sender' => [
                        'name' => (string) config('mail.from.name', config('app.name', 'WADO Events')),
                        'email' => (string) config('mail.from.address', 'noreply@wado-events.com'),
                    ],
                    'to' => [[
                        'email' => $user->email,
                        'name' => $user->name,
                    ]],
                    'subject' => 'Event Submitted - ' . $event->title,
                    'htmlContent' => $html,
                ]);

            if (! $response->successful()) {
                throw new \RuntimeException('Brevo API error ' . $response->status() . ': ' . $response->body());
            }
        } catch (\Throwable $e) {
            Log::warning('Event submission confirmation email failed.', [
                'event_id' => $event->id,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }

        return redirect()->route('home')->with('success', 'Your event has been submitted for review. We\'ll notify you once it\'s approved.');
    }

    public function createCategory(Request $request)
    {
        // Always return JSON for async category creation requests
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();
        $normalizedName = trim((string) $validated['name']);

        $existingCategory = \App\Models\Category::query()
            ->whereRaw('LOWER(name) = ?', [Str::lower($normalizedName)])
            ->first();

        if ($existingCategory) {
            return response()->json([
                'success' => true,
                'existing' => true,
                'category' => [
                    'id' => $existingCategory->id,
                    'name' => $existingCategory->name,
                ],
            ], 200);
        }

        try {
            // Create the new category
            $category = \App\Models\Category::create([
                'name' => $normalizedName,
                'slug' => Str::slug($normalizedName) . '-' . Str::lower(Str::random(4)),
            ]);

            return response()->json([
                'success' => true,
                'category' => [
                    'id' => $category->id,
                    'name' => $category->name,
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create category. Please try again.',
            ], 500);
        }
    }
}
