<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Event;
use App\Support\StaticEventCatalog;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
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

        // ── Site settings ─────────────────────────────────────────────
        $settingsPath = storage_path('app/site-settings.json');
        $s = file_exists($settingsPath)
            ? (json_decode(file_get_contents($settingsPath), true) ?? [])
            : [];

        $heroTitle    = $s['hero_title']    ?? 'Discover Unforgettable Events Near You';
        $heroSubtitle = $s['hero_subtitle'] ?? 'Concerts, sports, workshops & more — book your spot in seconds.';

        $defaultBanners = [
            asset('images/home-hero-bg.jpg'),
            asset('images/hero-image2.jpg'),
            asset('images/hero-image3.jfif'),
        ];
        $heroImages = [];
        for ($i = 1; $i <= 3; $i++) {
            $stored = $s["hero_banner_{$i}"] ?? null;
            $heroImages[] = $stored ? Storage::url($stored) : $defaultBanners[$i - 1];
        }

        $defaultPackages = [
            ['image' => asset('images/wrist-ticket.jpg'),  'label' => 'VIP Wristband Tickets',               'title' => 'Give your VIP guests a premium entry experience',          'copy' => 'With Our printed VIP wristbands, cleaner access control.'],
            ['image' => asset('images/cutout-ticket.jpg'), 'label' => 'Gate-Sale Ticket Printing',            'title' => 'Print ticket batches for fast sales at the entrance',       'copy' => 'Generate tickets in bulk, and sell them at entry with optional scanner support when you need more control.'],
            ['image' => asset('images/Online ticket.jpg'), 'label' => 'Online Ticketing & Event Management',  'title' => 'Sell online and let us manage your event..',                'copy' => 'Let customers buy tickets online while our team manages verification, attendance, and event flow from one organized system.'],
        ];

        $packageSlides = isset($s['packages']) && count($s['packages'])
            ? array_map(fn ($p) => [
                'image' => !empty($p['image']) ? Storage::url($p['image']) : null,
                'label' => $p['label'] ?? '',
                'title' => $p['title'] ?? '',
                'copy'  => $p['copy']  ?? '',
            ], $s['packages'])
            : $defaultPackages;

        return view('pages.home', [
            'featuredEvents' => $featuredEvents,
            'categoryPills'  => $categoryPills,
            'heroTitle'      => $heroTitle,
            'heroSubtitle'   => $heroSubtitle,
            'heroImages'     => $heroImages,
            'packageSlides'  => $packageSlides,
        ]);
    }
}
