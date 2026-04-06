<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Event;
use App\Models\TicketCategory;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@wado.test',
            'role' => 'admin',
        ]);

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => 'customer',
        ]);

        $categories = collect([
            ['name' => 'Music', 'description' => 'Concerts, live sessions, and performance nights.'],
            ['name' => 'Conference', 'description' => 'Professional events, summits, and networking sessions.'],
            ['name' => 'Film', 'description' => 'Premieres, screenings, and cinema experiences.'],
        ])->mapWithKeys(function (array $category) {
            $record = Category::create([
                ...$category,
                'slug' => Str::slug($category['name']).'-'.Str::lower(Str::random(4)),
            ]);

            return [$record->name => $record];
        });

        $events = [
            [
                'title' => 'Live Music Night',
                'category_name' => 'Music',
                'venue' => 'Kampala Serena Hotel',
                'city' => 'Kampala',
                'country' => 'Uganda',
                'description' => 'An energetic live music showcase featuring emerging artists, premium sound, and a full night of performances.',
                'starts_at' => Carbon::now()->addDays(7)->setTime(19, 0),
                'ends_at' => Carbon::now()->addDays(7)->setTime(23, 0),
                'status' => 'published',
                'image_url' => '/images/music.jpg',
                'is_featured' => true,
                'ticket_categories' => [
                    ['name' => 'VIP', 'price' => 80000, 'ticket_count' => 120, 'description' => 'Priority entrance and premium seating.'],
                    ['name' => 'Ordinary', 'price' => 50000, 'ticket_count' => 230, 'description' => 'Standard event access.'],
                ],
            ],
            [
                'title' => 'Business Connect 2026',
                'category_name' => 'Conference',
                'venue' => 'Speke Resort',
                'city' => 'Kampala',
                'country' => 'Uganda',
                'description' => 'A full-day networking and insights conference bringing entrepreneurs, investors, and operators together.',
                'starts_at' => Carbon::now()->addDays(14)->setTime(9, 0),
                'ends_at' => Carbon::now()->addDays(14)->setTime(17, 30),
                'status' => 'published',
                'image_url' => '/images/conference.jpg',
                'is_featured' => true,
                'ticket_categories' => [
                    ['name' => 'Executive', 'price' => 250000, 'ticket_count' => 120, 'description' => 'Front seating and VIP networking lounge.'],
                    ['name' => 'Standard', 'price' => 150000, 'ticket_count' => 380, 'description' => 'Full conference access.'],
                ],
            ],
            [
                'title' => 'Film Premiere Weekend',
                'category_name' => 'Film',
                'venue' => 'Century Cinemax',
                'city' => 'Kampala',
                'country' => 'Uganda',
                'description' => 'A premiere screening experience with red carpet arrivals, exclusive previews, and filmmaker conversations.',
                'starts_at' => Carbon::now()->addDays(21)->setTime(18, 30),
                'ends_at' => Carbon::now()->addDays(21)->setTime(22, 0),
                'status' => 'published',
                'image_url' => '/images/movie.jpg',
                'is_featured' => false,
                'ticket_categories' => [
                    ['name' => 'Red Carpet', 'price' => 60000, 'ticket_count' => 40, 'description' => 'Red carpet access and premium screening seats.'],
                    ['name' => 'Ordinary', 'price' => 30000, 'ticket_count' => 180, 'description' => 'General screening access.'],
                ],
            ],
        ];

        foreach ($events as $event) {
            $ticketCategories = collect($event['ticket_categories']);

            $createdEvent = Event::create([
                ...collect($event)->except(['category_name', 'ticket_categories'])->all(),
                'user_id' => $admin->id,
                'category_id' => $categories[$event['category_name']]->id,
                'slug' => Str::slug($event['title']).'-'.Str::lower(Str::random(6)),
                'ticket_price' => $ticketCategories->min('price'),
                'capacity' => $ticketCategories->sum('ticket_count'),
                'tickets_available' => $ticketCategories->sum('ticket_count'),
            ]);

            $ticketCategories->values()->each(function (array $ticketCategory, int $index) use ($createdEvent): void {
                TicketCategory::create([
                    'event_id' => $createdEvent->id,
                    'name' => $ticketCategory['name'],
                    'price' => $ticketCategory['price'],
                    'ticket_count' => $ticketCategory['ticket_count'],
                    'tickets_remaining' => $ticketCategory['ticket_count'],
                    'description' => $ticketCategory['description'],
                    'sort_order' => $index,
                ]);
            });
        }
    }
}
