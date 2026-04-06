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
        $admin = User::query()->updateOrCreate(
            ['email' => 'admin@wado.test'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
                'role' => 'admin',
            ],
        );

        User::query()->updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
                'role' => 'customer',
            ],
        );

        $categories = collect([
            ['name' => 'Music', 'description' => 'Concerts, live sessions, and performance nights.'],
            ['name' => 'Conference', 'description' => 'Professional events, summits, and networking sessions.'],
            ['name' => 'Film', 'description' => 'Premieres, screenings, and cinema experiences.'],
        ])->mapWithKeys(function (array $category) {
            $record = Category::query()->updateOrCreate(
                ['name' => $category['name']],
                [
                    ...$category,
                    'slug' => Str::slug($category['name']),
                ],
            );

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

            $createdEvent = Event::query()->updateOrCreate(
                ['title' => $event['title']],
                [
                    ...collect($event)->except(['category_name', 'ticket_categories'])->all(),
                    'user_id' => $admin->id,
                    'category_id' => $categories[$event['category_name']]->id,
                    'slug' => Str::slug($event['title']),
                    'ticket_price' => $ticketCategories->min('price'),
                    'capacity' => $ticketCategories->sum('ticket_count'),
                    'tickets_available' => $ticketCategories->sum('ticket_count'),
                ],
            );

            $createdEvent->ticketCategories()->delete();

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
