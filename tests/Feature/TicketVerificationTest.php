<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class TicketVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_gate_staff_can_verify_ticket_and_mark_it_used(): void
    {
        $staff = User::factory()->create([
            'role' => 'gate',
        ]);

        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        $category = Category::query()->create([
            'name' => 'Festival ' . Str::random(4),
            'slug' => 'festival-' . Str::lower(Str::random(6)),
            'description' => 'Category for tests',
        ]);

        $event = Event::query()->create([
            'user_id' => $staff->id,
            'category_id' => $category->id,
            'title' => 'Gate Event ' . Str::random(6),
            'slug' => 'gate-event-' . Str::lower(Str::random(8)),
            'venue' => 'Main Arena',
            'city' => 'Kampala',
            'country' => 'Uganda',
            'description' => 'Verification test event',
            'starts_at' => now()->addDay(),
            'ends_at' => now()->addDays(2),
            'ticket_price' => 20000,
            'capacity' => 200,
            'tickets_available' => 199,
            'status' => 'published',
        ]);

        $ticketCategory = TicketCategory::query()->create([
            'event_id' => $event->id,
            'name' => 'VIP',
            'price' => 20000,
            'ticket_count' => 200,
            'tickets_remaining' => 199,
            'sort_order' => 1,
        ]);

        $ticket = Ticket::query()->create([
            'user_id' => $customer->id,
            'event_id' => $event->id,
            'ticket_category_id' => $ticketCategory->id,
            'holder_name' => 'Ticket Holder',
            'ticket_code' => 'WADO-' . Str::upper(Str::random(10)),
            'quantity' => 1,
            'unit_price' => 20000,
            'total_amount' => 20000,
            'payment_provider' => 'mtn',
            'status' => Ticket::STATUS_CONFIRMED,
            'purchased_at' => now(),
        ]);

        $response = $this
            ->actingAs($staff)
            ->post(route('tickets.verify.store'), [
                'selected_event_id' => $event->id,
                'ticket_code' => $ticket->ticket_code,
                'mark_as_used' => true,
            ]);

        $response->assertRedirect(route('tickets.verify.index', ['event_id' => $event->id]));

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'status' => Ticket::STATUS_USED,
        ]);

        $this->assertDatabaseHas('ticket_scan_logs', [
            'ticket_id' => $ticket->id,
            'staff_user_id' => $staff->id,
            'ticket_code' => $ticket->ticket_code,
            'result' => 'valid',
        ]);
    }
}
