<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Event;
use App\Models\PaymentTransaction;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;

class CheckoutFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_free_ticket_issuance_completes_for_authenticated_user(): void
    {
        $user = User::factory()->create(['role' => 'customer']);
        $event = $this->makeEventWithStatus('published');
        $ticketCategory = $this->makeTicketCategory($event, 0, 5);

        $response = $this
            ->actingAs($user)
            ->post(route('checkout.store', $event), [
                'ticket_category_id' => $ticketCategory->id,
                'quantity' => 1,
                'holder_name' => 'Free Buyer',
                'idempotency_key' => strtoupper((string) Str::uuid()),
            ]);

        $ticket = Ticket::query()->where('event_id', $event->id)->first();

        $response->assertRedirect(route('tickets.show', $ticket));
        $this->assertNotNull($ticket);
        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'user_id' => $user->id,
            'status' => Ticket::STATUS_CONFIRMED,
            'payment_provider' => 'free',
        ]);
        $this->assertDatabaseHas('ticket_categories', [
            'id' => $ticketCategory->id,
            'tickets_remaining' => 4,
        ]);
    }

    public function test_oversell_prevention_rejects_second_buyer(): void
    {
        $event = $this->makeEventWithStatus('published');
        $ticketCategory = $this->makeTicketCategory($event, 0, 1);

        $first = $this->post(route('checkout.store', $event), [
            'ticket_category_id' => $ticketCategory->id,
            'quantity' => 1,
            'holder_name' => 'Buyer One',
            'email' => 'buyer1@example.com',
            'idempotency_key' => strtoupper((string) Str::uuid()),
        ]);
        $first->assertStatus(302);

        $second = $this
            ->from(route('checkout.create', $event))
            ->post(route('checkout.store', $event), [
                'ticket_category_id' => $ticketCategory->id,
                'quantity' => 1,
                'holder_name' => 'Buyer Two',
                'email' => 'buyer2@example.com',
                'idempotency_key' => strtoupper((string) Str::uuid()),
            ]);

        $second->assertStatus(302);
        $second->assertSessionHasErrors('quantity');
        $this->assertDatabaseHas('ticket_categories', [
            'id' => $ticketCategory->id,
            'tickets_remaining' => 0,
        ]);
    }

    public function test_guest_checkout_creates_user_and_issues_ticket(): void
    {
        $event = $this->makeEventWithStatus('published');
        $ticketCategory = $this->makeTicketCategory($event, 0, 3);

        $response = $this->post(route('checkout.store', $event), [
            'ticket_category_id' => $ticketCategory->id,
            'quantity' => 1,
            'holder_name' => 'Guest Holder',
            'email' => 'guest@example.com',
            'idempotency_key' => strtoupper((string) Str::uuid()),
        ]);

        $response->assertRedirect(route('checkout.confirmed'));
        $this->assertDatabaseHas('users', [
            'email' => 'guest@example.com',
            'role' => 'customer',
        ]);
        $this->assertDatabaseHas('tickets', [
            'event_id' => $event->id,
            'holder_name' => 'Guest Holder',
            'status' => Ticket::STATUS_CONFIRMED,
        ]);
    }

    public function test_paid_flow_initiates_stk_push(): void
    {
        Http::fake([
            '*' => Http::response([
                'status' => 'success',
                'message' => 'Collection initiated',
                'data' => [
                    'transaction' => [
                        'status' => 'pending',
                        'reference' => 'REF-TEST-123',
                    ],
                ],
            ], 200),
        ]);

        config([
            'services.marzepay.base_url' => 'https://wallet.wearemarz.com/api/v1',
            'services.marzepay.api_key' => 'key',
            'services.marzepay.api_secret' => 'secret',
        ]);

        $user = User::factory()->create(['role' => 'customer']);
        $event = $this->makeEventWithStatus('published');
        $ticketCategory = $this->makeTicketCategory($event, 5000, 10);

        $response = $this
            ->actingAs($user)
            ->post(route('checkout.store', $event), [
                'ticket_category_id' => $ticketCategory->id,
                'quantity' => 1,
                'holder_name' => 'Paid Buyer',
                'email' => 'paid@example.com',
                'payment_provider' => 'mtn',
                'phone_number' => '0771234567',
                'idempotency_key' => strtoupper((string) Str::uuid()),
            ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('payment_transactions', [
            'event_id' => $event->id,
            'user_id' => $user->id,
            'status' => PaymentTransaction::STATUS_PENDING,
        ]);

        Http::assertSent(fn ($request) => str_contains($request->url(), '/collect-money'));
    }

    public function test_checkout_store_returns_404_for_draft_event(): void
    {
        $event = $this->makeEventWithStatus('draft');
        $ticketCategory = $this->makeTicketCategory($event, 5000, 100);

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
        $ticketCategory = $this->makeTicketCategory($event, 5000, 100);

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

    protected function makeTicketCategory(Event $event, float $price, int $ticketCount): TicketCategory
    {
        return TicketCategory::query()->create([
            'event_id' => $event->id,
            'name' => 'Regular',
            'price' => $price,
            'ticket_count' => $ticketCount,
            'tickets_remaining' => $ticketCount,
            'sort_order' => 1,
        ]);
    }
}
