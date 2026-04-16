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

    public function test_fake_qr_signature_is_rejected_and_logged(): void
    {
        $context = $this->makeVerificationContext();
        $staff = $context['staff'];
        $event = $context['event'];
        $ticket = $context['ticket'];

        $response = $this
            ->actingAs($staff)
            ->post(route('tickets.verify.store'), [
                'selected_event_id' => $event->id,
                'scanned_payload' => json_encode([
                    'code' => $ticket->ticket_code,
                    'sig' => 'invalid-signature',
                ], JSON_UNESCAPED_SLASHES),
            ]);

        $response->assertRedirect(route('tickets.verify.index', ['event_id' => $event->id]));

        $this->assertDatabaseHas('ticket_scan_logs', [
            'ticket_id' => null,
            'ticket_code' => $ticket->ticket_code,
            'result' => 'fake',
        ]);
    }

    public function test_ticket_from_wrong_event_is_rejected(): void
    {
        $context = $this->makeVerificationContext();
        $staff = $context['staff'];
        $ticket = $context['ticket'];

        $otherEvent = Event::query()->create([
            'user_id' => $staff->id,
            'category_id' => $context['category']->id,
            'title' => 'Other Event ' . Str::random(6),
            'slug' => 'other-event-' . Str::lower(Str::random(8)),
            'venue' => 'Other Arena',
            'city' => 'Kampala',
            'country' => 'Uganda',
            'description' => 'Other event',
            'starts_at' => now()->addDay(),
            'ends_at' => now()->addDays(2),
            'ticket_price' => 20000,
            'capacity' => 200,
            'tickets_available' => 200,
            'status' => 'published',
        ]);

        $response = $this
            ->actingAs($staff)
            ->post(route('tickets.verify.store'), [
                'selected_event_id' => $otherEvent->id,
                'ticket_code' => $ticket->ticket_code,
                'mark_as_used' => true,
            ]);

        $response->assertRedirect(route('tickets.verify.index', ['event_id' => $otherEvent->id]));
        $this->assertDatabaseHas('ticket_scan_logs', [
            'ticket_id' => $ticket->id,
            'ticket_code' => $ticket->ticket_code,
            'result' => 'rejected',
        ]);
    }

    public function test_already_used_ticket_is_rejected(): void
    {
        $context = $this->makeVerificationContext();
        $staff = $context['staff'];
        $event = $context['event'];
        $ticket = $context['ticket'];

        $ticket->forceFill([
            'status' => Ticket::STATUS_USED,
            'used_at' => now()->subMinute(),
        ])->save();

        $response = $this
            ->actingAs($staff)
            ->post(route('tickets.verify.store'), [
                'selected_event_id' => $event->id,
                'ticket_code' => $ticket->ticket_code,
            ]);

        $response->assertRedirect(route('tickets.verify.index', ['event_id' => $event->id]));
        $this->assertDatabaseHas('ticket_scan_logs', [
            'ticket_id' => $ticket->id,
            'ticket_code' => $ticket->ticket_code,
            'result' => 'already-used',
        ]);
    }

    public function test_gate_staff_can_verify_ticket_and_mark_it_used(): void
    {
        $context = $this->makeVerificationContext();
        $staff = $context['staff'];
        $event = $context['event'];
        $ticket = $context['ticket'];

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

    /**
     * @return array{staff: User, customer: User, category: Category, event: Event, ticket_category: TicketCategory, ticket: Ticket}
     */
    protected function makeVerificationContext(): array
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

        return [
            'staff' => $staff,
            'customer' => $customer,
            'category' => $category,
            'event' => $event,
            'ticket_category' => $ticketCategory,
            'ticket' => $ticket,
        ];
    }
}
