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
        // Load site settings
        $settingsPath = storage_path('app/site-settings.json');
        $settings = file_exists($settingsPath)
            ? (json_decode(file_get_contents($settingsPath), true) ?? [])
            : [];

        // Hero content
        $heroTitle = $settings['hero_title'] ?? 'Discover Unforgettable Events Near You';
        $heroSubtitle = $settings['hero_subtitle'] ?? 'Concerts, sports, workshops & more — book your spot in seconds.';
        
        // Fix hero banners - properly handle array values from file uploads
        $heroImages = [];
        for ($i = 1; $i <= 3; $i++) {
            $bannerKey = "hero_banner_{$i}";
            $banner = $settings[$bannerKey] ?? null;
            
            // Normalize if it's an array (from file upload)
            if (is_array($banner)) {
                $banner = !empty($banner) ? reset($banner) : null;
            }
            
            // Convert storage path to URL
            if ($banner && is_string($banner) && !empty($banner)) {
                $heroImages[] = Storage::disk('public')->url($banner);
            } else {
                $heroImages[] = asset('images/default-hero.jpg');
            }
        }

        // Featured events
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

        // Category pills
        $categoryPills = Cache::remember('home:category_pills', 3600, function () {
            return Category::query()
                ->withCount(['events' => fn ($q) => $q->where('status', 'published')])
                ->orderBy('name')
                ->get()
                ->filter(fn (Category $category) => $category->events_count > 0)
                ->mapWithKeys(function (Category $category) {
                    return [
                        Str::slug($category->name) => [
                            'label' => $category->name,
                            'count' => (int) $category->events_count,
                        ],
                    ];
                });
        });

        // Package slides with comprehensive array-to-string fix
        $defaultPackages = [
            [
                'image' => asset('images/VIP wristband.jpg'),
                'label' => 'VIP Wristband Tickets',
                'title' => 'Give your VIP guests a premium entry experience',
                'copy'  => 'With our printed VIP wristbands, cleaner access control.',
                'price' => '',
            ],
            [
                'image' => asset('images/Gate-Sale Ticket.jpg'),
                'label' => 'Gate-Sale Ticket Printing',
                'title' => 'Print ticket batches for fast sales at the entrance',
                'copy'  => 'Generate tickets in bulk, and sell them at entry with optional scanner support.',
                'price' => '',
            ],
            [
                'image' => asset('images/Online ticket.jpg'),
                'label' => 'Online Ticketing & Event Management',
                'title' => 'Sell online and let us manage your event.',
                'copy'  => 'Let customers buy tickets online while our team manages verification, attendance, and event flow from one organized system.',
                'price' => '',
            ],
        ];

        $packageSlides = isset($settings['packages']) && is_array($settings['packages']) && count($settings['packages']) > 0
            ? array_map(function ($p) {
                // Fix for array to string conversion error
                $imagePath = $p['image'] ?? null;
                
                // Handle if image is stored as an array
                if (is_array($imagePath)) {
                    // Check if array has at least one element before accessing index 0
                    $imagePath = !empty($imagePath) && isset($imagePath[0]) ? $imagePath[0] : null;
                }
                
                // Handle if image is empty or null
                $imageUrl = null;
                if ($imagePath && is_string($imagePath) && !empty($imagePath)) {
                    // Check if it's already a full URL or a storage path
                    if (filter_var($imagePath, FILTER_VALIDATE_URL)) {
                        $imageUrl = $imagePath;
                    } else {
                        $imageUrl = Storage::disk('public')->url($imagePath);
                    }
                }
                
                return [
                    'image' => $imageUrl,
                    'label' => $p['label'] ?? '',
                    'title' => $p['title'] ?? '',
                    'copy'  => $p['copy'] ?? '',
                    'price' => $p['price'] ?? '',
                ];
            }, $settings['packages'])
            : $defaultPackages;

        return view('pages.home', [
            'heroImages' => $heroImages,
            'heroTitle' => $heroTitle,
            'heroSubtitle' => $heroSubtitle,
            'featuredEvents' => $featuredEvents,
            'categoryPills' => $categoryPills,
            'packageSlides' => $packageSlides,
        ]);
    }
}