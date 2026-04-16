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
                'role' => 'super_admin',
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

        $agent = User::query()->updateOrCreate(
            ['email' => 'agent@wado.test'],
            [
                'name' => 'Gate Agent Demo',
                'phone' => '+256700111222',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
                'role' => 'gate_agent',
            ],
        );

        $organisationOwner = User::query()->updateOrCreate(
            ['email' => 'org@wado.test'],
            [
                'name' => 'Organisation Owner Demo',
                'phone' => '+256700333444',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
                'role' => 'customer',
            ],
        );

        $categories = collect([
            ['name' => 'Music', 'description' => 'Concerts, live sessions, and performance nights.'],
            ['name' => 'Sports', 'description' => 'Matches, tournaments, and fan experiences.'],
            ['name' => 'Church', 'description' => 'Fellowship gatherings, worship services, and ministry events.'],
            ['name' => 'Charity', 'description' => 'Giving drives, support events, and social impact gatherings.'],
            ['name' => 'Musical Concerts', 'description' => 'Live performances, concerts, and entertainment experiences.'],
            ['name' => 'Fundraising', 'description' => 'Campaign launches, donor drives, and community fundraising events.'],
            ['name' => 'Educational', 'description' => 'Learning sessions, workshops, trainings, and academic events.'],
            ['name' => 'Gaming', 'description' => 'Esports, tournaments, gaming nights, and interactive competitions.'],
            ['name' => 'Conference', 'description' => 'Professional events, summits, and networking sessions.'],
            ['name' => 'Conferences', 'description' => 'Professional conferences, summits, and business networking sessions.'],
            ['name' => 'Kids Events', 'description' => 'Children-focused activities, fun days, and family entertainment.'],
            ['name' => 'Wellness', 'description' => 'Health, fitness, mindfulness, and wellbeing experiences.'],
            ['name' => 'Family', 'description' => 'Family-friendly outings, celebrations, and shared experiences.'],
            ['name' => 'Corporate Events', 'description' => 'Company meetings, launches, networking, and staff experiences.'],
            ['name' => 'Free Events', 'description' => 'Open-access community events with free entry.'],
            ['name' => 'Comedy', 'description' => 'Stand-up shows, comic performances, and laughter-filled nights.'],
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

        // Demo organisation-owned event for event_owner POV checks.
        $orgEvent = Event::query()->updateOrCreate(
            ['title' => 'Organisation Demo Summit'],
            [
                'user_id' => $organisationOwner->id,
                'category_id' => $categories['Conference']->id,
                'slug' => Str::slug('Organisation Demo Summit'),
                'venue' => 'UMA Multipurpose Hall',
                'city' => 'Kampala',
                'country' => 'Uganda',
                'description' => 'Seeded event owned by organisation account for role POV testing.',
                'starts_at' => Carbon::now()->addDays(10)->setTime(10, 0),
                'ends_at' => Carbon::now()->addDays(10)->setTime(18, 0),
                'ticket_price' => 100000,
                'capacity' => 250,
                'tickets_available' => 250,
                'status' => 'published',
                'image_url' => '/images/conference.jpg',
                'is_featured' => false,
            ],
        );

        $orgEvent->ticketCategories()->delete();

        TicketCategory::create([
            'event_id' => $orgEvent->id,
            'name' => 'General',
            'price' => 100000,
            'ticket_count' => 250,
            'tickets_remaining' => 250,
            'description' => 'Organisation demo ticket class.',
            'sort_order' => 0,
        ]);
    }
}
