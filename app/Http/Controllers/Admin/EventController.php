<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEventRequest;
use App\Models\Category;
use App\Models\Event;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::query()
            ->with(['organizer', 'category', 'ticketCategories', 'artists'])
            ->latest()
            ->get();

        return view('admin.events.index', [
            'events' => $events,
            'stats' => [
                'total' => $events->count(),
                'published' => $events->where('status', 'published')->count(),
                'drafts' => $events->where('status', 'draft')->count(),
                'capacity' => $events->sum('capacity'),
            ],
        ]);
    }

    public function create()
    {
        return view('admin.events.create', [
            'categories' => Category::query()->orderBy('name')->get(),
        ]);
    }

    public function store(StoreEventRequest $request)
    {
        $admin = User::query()->where('role', 'admin')->first();
        $data = $request->validated();
        $artists = collect($data['artists'] ?? [])->filter(fn (array $artist) => filled($artist['name'] ?? null));
        $ticketCategories = collect($data['ticket_categories']);
        $isFree = (bool) ($data['is_free'] ?? false);
        unset($data['artists']);
        unset($data['ticket_categories']);
        unset($data['image_file']);
        unset($data['is_free']);

        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('event-images', 'public');
            $data['image_url'] = '/storage/'.$path;
        }

        if ($isFree) {
            $ticketCategories = $ticketCategories->map(function (array $ticketCategory) {
                $ticketCategory['price'] = 0;

                return $ticketCategory;
            });
        }

        $data['slug'] = Str::slug($data['title']).'-'.Str::lower(Str::random(6));
        $data['user_id'] = $admin?->id;
        $data['capacity'] = $ticketCategories->sum('ticket_count');
        $data['tickets_available'] = $data['capacity'];
        $data['ticket_price'] = $ticketCategories->min('price');

        DB::transaction(function () use ($artists, $data, $ticketCategories): void {
            $event = Event::create($data);

            $artists->values()->each(function (array $artist, int $index) use ($event): void {
                $event->artists()->create([
                    'name' => $artist['name'],
                    'sort_order' => $index,
                ]);
            });

            $ticketCategories->values()->each(function (array $ticketCategory, int $index) use ($event): void {
                $event->ticketCategories()->create([
                    'name' => $ticketCategory['name'],
                    'price' => $ticketCategory['price'],
                    'ticket_count' => $ticketCategory['ticket_count'],
                    'tickets_remaining' => $ticketCategory['ticket_count'],
                    'description' => $ticketCategory['description'] ?? null,
                    'sort_order' => $index,
                ]);
            });
        });

        return redirect()
            ->route('admin.events.index')
            ->with('success', 'Event and ticket categories created successfully.');
    }
}
