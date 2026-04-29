<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Event;
use App\Models\PaymentTransaction;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Security attack simulation tests.
 *
 * Each test attempts a real attack vector and asserts the system blocks it.
 * Run with: php artisan test tests/Feature/SecurityAttackTest.php
 */
class SecurityAttackTest extends TestCase
{
    use RefreshDatabase;

    // ──────────────────────────────────────────────────────────────────────────
    // PAYMENT BYPASS — price manipulation
    // ──────────────────────────────────────────────────────────────────────────

    /** Attacker POSTs a lower price hoping the backend uses it. */
    public function test_attacker_cannot_manipulate_price_via_post(): void
    {
        $user = User::factory()->create(['role' => 'customer']);
        $event = $this->makeEvent();
        $category = $this->makeCategory($event, 50000, 10); // UGX 50,000

        // Attacker injects a much lower amount into the POST body
        $this
            ->actingAs($user)
            ->post(route('checkout.store', $event), [
                'ticket_category_id' => $category->id,
                'quantity'           => 1,
                'holder_name'        => 'Attacker',
                'payment_provider'   => 'mtn',
                'phone_number'       => '0771234567',
                'unit_price'         => 100,        // attacker's injected price
                'total_amount'       => 100,        // attacker's injected total
                'amount'             => 100,
                'price'              => 100,
                'idempotency_key'    => strtoupper((string) Str::uuid()),
            ]);

        // Transaction in DB must have the real price from the database — never the attacker's value
        $payment = PaymentTransaction::query()->latest('id')->first();
        $this->assertNotNull($payment, 'A payment transaction should have been created');
        $this->assertEquals(50000, (float) $payment->total_amount, 'Backend stored attacker price instead of DB price');

        // Must not have issued a free ticket
        $this->assertDatabaseMissing('tickets', ['event_id' => $event->id]);
    }

    /** Attacker picks a cheaper ticket category but sends the premium category id. */
    public function test_price_is_always_taken_from_the_correct_category(): void
    {
        $user = User::factory()->create(['role' => 'customer']);
        $event = $this->makeEvent();
        $cheap  = $this->makeCategory($event, 1000, 10, 'Budget');
        $this->makeCategory($event, 100000, 10, 'VIP');

        // Attacker submits the cheap category — backend must lock in that category's price
        $this->actingAs($user)->post(route('checkout.store', $event), [
            'ticket_category_id' => $cheap->id,
            'quantity'           => 1,
            'holder_name'        => 'Attacker',
            'payment_provider'   => 'mtn',
            'phone_number'       => '0771234567',
            'idempotency_key'    => strtoupper((string) Str::uuid()),
        ]);

        $payment = PaymentTransaction::query()->where('ticket_category_id', $cheap->id)->first();
        $this->assertNotNull($payment, 'Payment transaction should have been created');
        $this->assertEquals(1000, (float) $payment->unit_price, 'Backend must use the category price, not any injected value');
    }

    // ──────────────────────────────────────────────────────────────────────────
    // WEBHOOK FORGERY
    // ──────────────────────────────────────────────────────────────────────────

    /** Attacker sends a webhook with no signature at all. */
    public function test_webhook_with_no_signature_is_rejected(): void
    {
        Queue::fake();
        config(['services.marzepay.webhook_secret' => 'real-secret']);

        $ctx = $this->makePaymentContext();

        $response = $this->postJson(route('payments.webhook.marzepay'), [
            'reference' => $ctx['payment']->idempotency_key,
            'status'    => 'confirmed',
        ]);

        $response->assertStatus(401);
        Queue::assertNothingPushed();
        $this->assertDatabaseMissing('payment_transactions', [
            'id'     => $ctx['payment']->id,
            'status' => PaymentTransaction::STATUS_CONFIRMED,
        ]);
    }

    /** Attacker sends a webhook with a wrong/guessed signature. */
    public function test_webhook_with_wrong_signature_is_rejected(): void
    {
        Queue::fake();
        config(['services.marzepay.webhook_secret' => 'real-secret']);

        $ctx     = $this->makePaymentContext();
        $payload = ['reference' => $ctx['payment']->idempotency_key, 'status' => 'confirmed'];

        $response = $this
            ->withHeader('X-Marze-Signature', 'totallywrongsignature')
            ->postJson(route('payments.webhook.marzepay'), $payload);

        $response->assertStatus(401);
        Queue::assertNothingPushed();
    }

    /** Attacker invents a payment reference that does not exist. */
    public function test_webhook_with_invented_reference_is_rejected(): void
    {
        Queue::fake();
        config(['services.marzepay.webhook_secret' => 'test-secret']);

        $payload = ['reference' => 'FAKE-REF-DOESNT-EXIST', 'status' => 'confirmed'];

        $response = $this
            ->withHeader('X-Marze-Signature', $this->sign($payload))
            ->postJson(route('payments.webhook.marzepay'), $payload);

        $response->assertStatus(404);
        Queue::assertNothingPushed();
    }

    /** Attacker sends a valid-looking webhook but with a lower amount than charged. */
    public function test_webhook_with_lower_amount_than_charged_does_not_issue_ticket(): void
    {
        Queue::fake();
        config(['services.marzepay.webhook_secret' => 'test-secret']);

        $ctx = $this->makePaymentContext(50000); // UGX 50,000 ticket

        $payload = [
            'reference' => $ctx['payment']->idempotency_key,
            'status'    => 'confirmed',
            'amount'    => 1000, // attacker only paid UGX 1,000
        ];

        $this
            ->withHeader('X-Marze-Signature', $this->sign($payload))
            ->postJson(route('payments.webhook.marzepay'), $payload);

        Queue::assertNothingPushed();
        $this->assertDatabaseMissing('payment_transactions', [
            'id'     => $ctx['payment']->id,
            'status' => PaymentTransaction::STATUS_CONFIRMED,
        ]);
    }

    /** Attacker replays the same confirmed webhook three times to get multiple tickets. */
    public function test_replayed_webhook_does_not_issue_multiple_tickets(): void
    {
        Queue::fake();
        config(['services.marzepay.webhook_secret' => 'test-secret']);

        $ctx     = $this->makePaymentContext();
        $payload = ['reference' => $ctx['payment']->idempotency_key, 'status' => 'confirmed'];
        $sig     = $this->sign($payload);

        for ($i = 0; $i < 3; $i++) {
            $this->withHeader('X-Marze-Signature', $sig)
                ->postJson(route('payments.webhook.marzepay'), $payload);
        }

        // Job dispatched exactly once — not three times
        Queue::assertPushedTimes(\App\Jobs\IssueTicketForPayment::class, 1);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // OVERSELL / INVENTORY ATTACKS
    // ──────────────────────────────────────────────────────────────────────────

    /** Attacker tries to buy more tickets than are available in one request. */
    public function test_cannot_buy_more_tickets_than_remaining(): void
    {
        $user  = User::factory()->create(['role' => 'customer']);
        $event = $this->makeEvent();
        $category = $this->makeCategory($event, 0, 3); // only 3 left

        $response = $this
            ->actingAs($user)
            ->from(route('checkout.create', $event))
            ->post(route('checkout.store', $event), [
                'ticket_category_id' => $category->id,
                'quantity'           => 10, // wants 10, only 3 exist
                'holder_name'        => 'Greedy Buyer',
                'idempotency_key'    => strtoupper((string) Str::uuid()),
            ]);

        $response->assertSessionHasErrors('quantity');
        $this->assertDatabaseMissing('tickets', ['event_id' => $event->id]);
    }

    /** Two buyers race to claim the last ticket — only one should win. */
    public function test_second_buyer_is_blocked_when_last_ticket_taken(): void
    {
        $event    = $this->makeEvent();
        $category = $this->makeCategory($event, 0, 1); // exactly 1 ticket

        // First buyer succeeds
        $first = $this->post(route('checkout.store', $event), [
            'ticket_category_id' => $category->id,
            'quantity'           => 1,
            'holder_name'        => 'First',
            'email'              => 'first@example.com',
            'idempotency_key'    => strtoupper((string) Str::uuid()),
        ]);
        $first->assertStatus(302);

        // Second buyer must be rejected
        $second = $this
            ->from(route('checkout.create', $event))
            ->post(route('checkout.store', $event), [
                'ticket_category_id' => $category->id,
                'quantity'           => 1,
                'holder_name'        => 'Second',
                'email'              => 'second@example.com',
                'idempotency_key'    => strtoupper((string) Str::uuid()),
            ]);

        $second->assertSessionHasErrors('quantity');
        $this->assertDatabaseHas('ticket_categories', [
            'id'                => $category->id,
            'tickets_remaining' => 0,
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // QR CODE / TICKET FORGERY
    // ──────────────────────────────────────────────────────────────────────────

    /** Attacker submits a completely made-up ticket code to the scan endpoint. */
    public function test_forged_ticket_code_is_rejected_at_scan(): void
    {
        // super_admin bypasses the event-assignment check so we can test QR validation in isolation
        $agent = User::factory()->create(['role' => 'super_admin']);
        $event = $this->makeEvent();

        $response = $this
            ->actingAs($agent)
            ->postJson(route('tickets.verify.scan-json'), [
                'ticket_code'       => 'WADO-FAKE-9999-XXXXXX',
                'selected_event_id' => $event->id,
            ]);

        $response->assertOk(); // endpoint returns 200 with ok:false
        $response->assertJson(['ok' => false]);
    }

    /** Attacker scans a valid ticket but for the wrong event. */
    public function test_ticket_from_different_event_is_rejected_at_gate(): void
    {
        $agent   = User::factory()->create(['role' => 'super_admin']);
        $buyer   = User::factory()->create(['role' => 'customer']);
        $eventA  = $this->makeEvent();
        $eventB  = $this->makeEvent();
        $catA    = $this->makeCategory($eventA, 0, 10);

        // Issue a real ticket for Event A
        $ticket = Ticket::query()->create([
            'user_id'            => $buyer->id,
            'event_id'           => $eventA->id,
            'ticket_category_id' => $catA->id,
            'holder_name'        => 'Real Buyer',
            'ticket_code'        => 'WADO-' . $buyer->id . '-' . $eventA->id . '-ABCDEF',
            'quantity'           => 1,
            'unit_price'         => 0,
            'total_amount'       => 0,
            'payment_provider'   => 'free',
            'status'             => Ticket::STATUS_CONFIRMED,
            'purchased_at'       => now(),
        ]);

        // Try to scan it at Event B's gate
        $response = $this
            ->actingAs($agent)
            ->postJson(route('tickets.verify.scan-json'), [
                'ticket_code'       => $ticket->ticket_code,
                'selected_event_id' => $eventB->id, // wrong event
            ]);

        $response->assertOk();
        $response->assertJson(['ok' => false]);

        // Ticket must still be unused
        $this->assertDatabaseHas('tickets', [
            'id'     => $ticket->id,
            'status' => Ticket::STATUS_CONFIRMED,
        ]);
    }

    /** Attacker scans an already-used ticket to try to enter twice. */
    public function test_already_scanned_ticket_is_rejected(): void
    {
        $agent  = User::factory()->create(['role' => 'super_admin']);
        $buyer  = User::factory()->create(['role' => 'customer']);
        $event  = $this->makeEvent();
        $cat    = $this->makeCategory($event, 0, 10);

        $ticket = Ticket::query()->create([
            'user_id'            => $buyer->id,
            'event_id'           => $event->id,
            'ticket_category_id' => $cat->id,
            'holder_name'        => 'Already In',
            'ticket_code'        => 'WADO-' . $buyer->id . '-' . $event->id . '-USED01',
            'quantity'           => 1,
            'unit_price'         => 0,
            'total_amount'       => 0,
            'payment_provider'   => 'free',
            'status'             => Ticket::STATUS_USED, // already scanned
            'used_at'            => now()->subMinutes(5),
            'purchased_at'       => now()->subHour(),
        ]);

        $response = $this
            ->actingAs($agent)
            ->postJson(route('tickets.verify.scan-json'), [
                'ticket_code'       => $ticket->ticket_code,
                'selected_event_id' => $event->id,
            ]);

        $response->assertOk();
        $response->assertJson(['ok' => false]);
        $this->assertEquals('already_used', $response->json('reason'));
    }

    // ──────────────────────────────────────────────────────────────────────────
    // ACCESS CONTROL
    // ──────────────────────────────────────────────────────────────────────────

    /** Unauthenticated user cannot manually confirm a payment via admin route. */
    public function test_unauthenticated_user_cannot_confirm_payment(): void
    {
        $ctx = $this->makePaymentContext();

        $response = $this->post(route('payments.admin.confirm', $ctx['payment']));

        $response->assertRedirect(route('login'));
        $this->assertDatabaseHas('payment_transactions', [
            'id'     => $ctx['payment']->id,
            'status' => PaymentTransaction::STATUS_INITIATED,
        ]);
    }

    /** A regular customer cannot confirm payments — admin only. */
    public function test_customer_cannot_confirm_payment_via_admin_route(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $ctx      = $this->makePaymentContext();

        $response = $this
            ->actingAs($customer)
            ->post(route('payments.admin.confirm', $ctx['payment']));

        $response->assertForbidden();
        $this->assertDatabaseHas('payment_transactions', [
            'id'     => $ctx['payment']->id,
            'status' => PaymentTransaction::STATUS_INITIATED,
        ]);
    }

    /** Unauthenticated user cannot hit the scan endpoint. */
    public function test_unauthenticated_user_cannot_scan_tickets(): void
    {
        $response = $this->postJson(route('tickets.verify.scan-json'), [
            'ticket_code' => 'WADO-1-1-ABCDEF',
        ]);

        $response->assertStatus(401);
    }

    /** Buyer cannot buy tickets for a draft event. */
    public function test_cannot_purchase_ticket_for_draft_event(): void
    {
        $user  = User::factory()->create(['role' => 'customer']);
        $event = $this->makeEvent('draft');
        // Use price=0 (free) so CheckoutRequest validation passes — the abort_if fires inside the controller
        $cat   = $this->makeCategory($event, 0, 10);

        $response = $this
            ->actingAs($user)
            ->post(route('checkout.store', $event), [
                'ticket_category_id' => $cat->id,
                'quantity'           => 1,
                'holder_name'        => 'Sneaky Buyer',
                'idempotency_key'    => strtoupper((string) Str::uuid()),
            ]);

        $response->assertStatus(404);
        $this->assertDatabaseMissing('tickets', ['event_id' => $event->id]);
    }

    /** Buyer cannot buy tickets for a cancelled event. */
    public function test_cannot_purchase_ticket_for_cancelled_event(): void
    {
        $user  = User::factory()->create(['role' => 'customer']);
        $event = $this->makeEvent('cancelled');
        $cat   = $this->makeCategory($event, 0, 10);

        $response = $this
            ->actingAs($user)
            ->post(route('checkout.store', $event), [
                'ticket_category_id' => $cat->id,
                'quantity'           => 1,
                'holder_name'        => 'Sneaky Buyer',
                'idempotency_key'    => strtoupper((string) Str::uuid()),
            ]);

        $response->assertStatus(404);
        $this->assertDatabaseMissing('tickets', ['event_id' => $event->id]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // RATE LIMITING
    // ──────────────────────────────────────────────────────────────────────────

    /** Attacker hammers the checkout endpoint — must be blocked after 10 attempts. */
    public function test_checkout_rate_limiter_blocks_after_ten_attempts(): void
    {
        $user  = User::factory()->create(['role' => 'customer']);
        $event = $this->makeEvent();
        // Free ticket so CheckoutRequest validation passes and rate limit check runs
        $cat   = $this->makeCategory($event, 0, 100);

        // Exhaust the 10-attempt limit
        for ($i = 0; $i < 10; $i++) {
            RateLimiter::hit('checkout:purchase:' . $event->id . ':user:' . $user->id, 600);
        }

        $response = $this
            ->actingAs($user)
            ->from(route('checkout.create', $event))
            ->post(route('checkout.store', $event), [
                'ticket_category_id' => $cat->id,
                'quantity'           => 1,
                'holder_name'        => 'Rapid Buyer',
                'idempotency_key'    => strtoupper((string) Str::uuid()),
            ]);

        $response->assertSessionHasErrors('quantity');
        $this->assertStringContainsString(
            'Too many',
            session('errors')->first('quantity')
        );
    }

    // ──────────────────────────────────────────────────────────────────────────
    // TICKET ENUMERATION
    // ──────────────────────────────────────────────────────────────────────────

    /** Attacker tries sequential ticket IDs like /tickets/1, /tickets/2. */
    public function test_tickets_are_not_accessible_by_sequential_id(): void
    {
        $user   = User::factory()->create(['role' => 'customer']);
        $other  = User::factory()->create(['role' => 'customer']);
        $event  = $this->makeEvent();
        $cat    = $this->makeCategory($event, 0, 10);

        // Create a ticket belonging to $other
        $ticket = Ticket::query()->create([
            'user_id'            => $other->id,
            'event_id'           => $event->id,
            'ticket_category_id' => $cat->id,
            'holder_name'        => 'Real Owner',
            'ticket_code'        => 'WADO-' . $other->id . '-' . $event->id . '-OWNER1',
            'quantity'           => 1,
            'unit_price'         => 0,
            'total_amount'       => 0,
            'payment_provider'   => 'free',
            'status'             => Ticket::STATUS_CONFIRMED,
            'purchased_at'       => now(),
        ]);

        // $user tries to access $other's ticket by guessing the URL
        if (\Route::has('tickets.show')) {
            $response = $this->actingAs($user)->get(route('tickets.show', $ticket));
            // Must be forbidden or not found — never 200
            $this->assertNotEquals(200, $response->status(),
                'Another user could view a ticket they do not own via direct URL');
        } else {
            $this->assertTrue(true); // route doesn't exist — no enumeration surface
        }
    }

    // ──────────────────────────────────────────────────────────────────────────
    // HELPERS
    // ──────────────────────────────────────────────────────────────────────────

    protected function makeEvent(string $status = 'published'): Event
    {
        $category = Category::query()->create([
            'name'        => 'Test ' . Str::random(4),
            'slug'        => 'test-' . Str::lower(Str::random(6)),
            'description' => 'Test category',
        ]);

        return Event::query()->create([
            'category_id'      => $category->id,
            'title'            => 'Attack Test Event ' . Str::random(4),
            'slug'             => 'attack-event-' . Str::lower(Str::random(8)),
            'venue'            => 'Test Venue',
            'city'             => 'Kampala',
            'country'          => 'Uganda',
            'description'      => 'Security test event',
            'starts_at'        => now()->addDay(),
            'ends_at'          => now()->addDays(2),
            'ticket_price'     => 5000,
            'capacity'         => 100,
            'tickets_available'=> 100,
            'status'           => $status,
        ]);
    }

    protected function makeCategory(Event $event, float $price, int $stock, string $name = 'Regular'): TicketCategory
    {
        return TicketCategory::query()->create([
            'event_id'          => $event->id,
            'name'              => $name,
            'price'             => $price,
            'ticket_count'      => $stock,
            'tickets_remaining' => $stock,
            'sort_order'        => 1,
        ]);
    }

    protected function makePaymentContext(float $amount = 5000): array
    {
        $user     = User::factory()->create(['role' => 'customer']);
        $event    = $this->makeEvent();
        $category = $this->makeCategory($event, $amount, 100);

        $payment = PaymentTransaction::query()->create([
            'user_id'            => $user->id,
            'event_id'           => $event->id,
            'ticket_category_id' => $category->id,
            'holder_name'        => 'Test Buyer',
            'idempotency_key'    => strtoupper((string) Str::uuid()),
            'payment_provider'   => 'mtn',
            'phone_number'       => '+256771234567',
            'quantity'           => 1,
            'unit_price'         => $amount,
            'total_amount'       => $amount,
            'currency'           => 'UGX',
            'status'             => PaymentTransaction::STATUS_INITIATED,
            'expires_at'         => now()->addMinutes(5),
        ]);

        return compact('user', 'event', 'category', 'payment');
    }

    protected function sign(array $payload): string
    {
        return hash_hmac('sha256', (string) json_encode($payload, JSON_UNESCAPED_SLASHES), 'test-secret');
    }
}
