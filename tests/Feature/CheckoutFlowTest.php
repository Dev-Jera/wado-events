<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Event;
use App\Models\TicketCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class CheckoutFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_store_returns_404_for_draft_event(): void
    {
        $event = $this->makeEventWithStatus('draft');
        $ticketCategory = TicketCategory::query()->create([
            'event_id' => $event->id,
            'name' => 'Regular',
            'price' => 5000,
            'ticket_count' => 100,
            'tickets_remaining' => 100,
            'sort_order' => 1,
        ]);

        $response = $this->post(route('checkout.store', $event), [
            'ticket_category_id' => $ticketCategory->id,
            'quantity' => 1,
            'holder_name' => 'Draft Buyer',
            'email' => 'draft@example.com',
            'payment_provider' => 'mtn',
            'phone_number' => '+256771234567',
            'idempotency_key' => strtoupper((string) Str::uuid()),
        ]);

        $response->assertNotFound();
    }

    public function test_checkout_store_returns_404_for_cancelled_event(): void
    {
        $event = $this->makeEventWithStatus('cancelled');
        $ticketCategory = TicketCategory::query()->create([
            'event_id' => $event->id,
            'name' => 'Regular',
            'price' => 5000,
            'ticket_count' => 100,
            'tickets_remaining' => 100,
            'sort_order' => 1,
        ]);

        $response = $this->post(route('checkout.store', $event), [
            'ticket_category_id' => $ticketCategory->id,
            'quantity' => 1,
            'holder_name' => 'Cancelled Buyer',
            'email' => 'cancelled@example.com',
            'payment_provider' => 'mtn',
            'phone_number' => '+256771234567',
            'idempotency_key' => strtoupper((string) Str::uuid()),
        ]);

        $response->assertNotFound();
    }

    protected function makeEventWithStatus(string $status): Event
    {
        $category = Category::query()->create([
            'name' => 'Music ' . Str::random(4),
            'slug' => 'music-' . Str::lower(Str::random(6)),
            'description' => 'Category for tests',
        ]);

        return Event::query()->create([
            'category_id' => $category->id,
            'title' => 'Test Event ' . Str::random(6),
            'slug' => 'test-event-' . Str::lower(Str::random(8)),
            'venue' => 'Test Venue',
            'city' => 'Kampala',
            'country' => 'Uganda',
            'description' => 'Test description',
            'starts_at' => now()->addDay(),
            'ends_at' => now()->addDays(2),
            'ticket_price' => 5000,
            'capacity' => 100,
            'tickets_available' => 100,
            'status' => $status,
        ]);
    }
}
