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
        
        $heroBanners = array_filter([
            $settings['hero_banner_1'] ?? null,
            $settings['hero_banner_2'] ?? null,
            $settings['hero_banner_3'] ?? null,
        ]);
        
        $heroImages = array_map(function ($banner) {
            // Check if $banner is an array and extract the first element, or use null
            if (is_array($banner)) {
                $banner = !empty($banner) ? reset($banner) : null; // Get the first element of the array
            }

            return $banner ? Storage::url($banner) : asset('images/default-hero.jpg'); // Provide a default image
        }, $heroBanners);

        // Ensure there are at least 3 images
        while (count($heroImages) < 3) {
            $heroImages[] = asset('images/default-hero.jpg'); // Add default images if less than 3
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

        // Package slides with array-to-string fix
        $defaultPackages = [
            [
                'image' => asset('images/VIP wristband.jpg'),
                'label' => 'VIP Wristband Tickets',
                'title' => 'Give your VIP guests a premium entry experience',
                'copy' => 'With our printed VIP wristbands, cleaner access control.'
            ],
            [
                'image' => asset('images/Gate-Sale Ticket.jpg'),
                'label' => 'Gate-Sale Ticket Printing',
                'title' => 'Print ticket batches for fast sales at the entrance',
                'copy' => 'Generate tickets in bulk, and sell them at entry with optional scanner support.'
            ],
            [
                'image' => asset('images/Online ticket.jpg'),
                'label' => 'Online Ticketing & Event Management',
                'title' => 'Sell online and let us manage your event.',
                'copy' => 'Let customers buy tickets online while our team manages verification, attendance, and event flow from one organized system.'
            ],
        ];

        $packageSlides = isset($settings['packages']) && count($settings['packages'])
            ? array_map(function ($p) {
                // Fix for array to string conversion error
                $imagePath = $p['image'] ?? null;
                
                // Handle if image is stored as an array
                if (is_array($imagePath)) {
                    $imagePath = !empty($imagePath) ? reset($imagePath) : null;
                }
                
                // Handle if image is empty or null
                if (empty($imagePath)) {
                    $imageUrl = null;
                } else {
                    // Check if it's already a full URL or a storage path
                    $imageUrl = filter_var($imagePath, FILTER_VALIDATE_URL) 
                        ? $imagePath 
                        : (is_string($imagePath) ? Storage::url($imagePath) : null);
                }
                
                return [
                    'image' => $imageUrl,
                    'label' => $p['label'] ?? '',
                    'title' => $p['title'] ?? '',
                    'copy'  => $p['copy'] ?? '',
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