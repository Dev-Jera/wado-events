<?php

namespace App\Support;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class StaticEventCatalog
{
    public static function events(): Collection
    {
        return collect([
            [
                'title' => 'Live Music Night',
                'category_name' => 'Music',
                'venue' => 'Kampala Serena Hotel',
                'city' => 'Kampala',
                'country' => 'Uganda',
                'description' => 'An energetic live music showcase featuring emerging artists, premium sound, and a full night of performances.',
                'starts_at' => Carbon::now()->addDays(7)->setTime(19, 0),
                'ticket_price' => 50000,
                'capacity' => 350,
                'tickets_available' => 350,
                'status' => 'published',
                'image_url' => '/images/music.jpg',
                'is_featured' => true,
            ],
            [
                'title' => 'Business Connect 2026',
                'category_name' => 'Conference',
                'venue' => 'Speke Resort',
                'city' => 'Kampala',
                'country' => 'Uganda',
                'description' => 'A full-day networking and insights conference bringing entrepreneurs, investors, and operators together.',
                'starts_at' => Carbon::now()->addDays(14)->setTime(9, 0),
                'ticket_price' => 150000,
                'capacity' => 500,
                'tickets_available' => 500,
                'status' => 'published',
                'image_url' => '/images/conference.jpg',
                'is_featured' => true,
            ],
            [
                'title' => 'Film Premiere Weekend',
                'category_name' => 'Film',
                'venue' => 'Century Cinemax',
                'city' => 'Kampala',
                'country' => 'Uganda',
                'description' => 'A premiere screening experience with red carpet arrivals, exclusive previews, and filmmaker conversations.',
                'starts_at' => Carbon::now()->addDays(21)->setTime(18, 30),
                'ticket_price' => 30000,
                'capacity' => 220,
                'tickets_available' => 220,
                'status' => 'published',
                'image_url' => '/images/movie.jpg',
                'is_featured' => false,
            ],
        ])->map(function (array $event) {
            return (object) [
                'id' => null,
                ...$event,
                'category_label' => $event['category_name'],
                'live_status' => Carbon::parse($event['starts_at'])->isFuture() ? 'upcoming' : 'live',
                'url' => route('events.index'),
            ];
        });
    }
}
