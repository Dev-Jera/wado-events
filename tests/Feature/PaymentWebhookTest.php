<?php

namespace Tests\Feature;

use App\Jobs\IssueTicketForPayment;
use App\Models\Category;
use App\Models\Event;
use App\Models\PaymentTransaction;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Tests\TestCase;

class PaymentWebhookTest extends TestCase
{
    use RefreshDatabase;

    public function test_webhook_failed_callback_releases_inventory(): void
    {
        config(['services.marzepay.webhook_secret' => 'test-secret']);

        $records = $this->makePaymentContext();
        $payment = $records['payment'];
        $event = $records['event'];
        $ticketCategory = $records['ticket_category'];

        $ticketCategory->forceFill(['tickets_remaining' => 99])->save();
        $event->forceFill(['tickets_available' => 99])->save();

        $payload = [
            'reference' => $payment->idempotency_key,
            'status' => 'failed',
            'message' => 'Declined by user',
        ];

        $response = $this
            ->withHeader('X-Marze-Signature', $this->signPayload($payload))
            ->postJson(route('payments.webhook.marzepay'), $payload);

        $response->assertOk()->assertJson([
            'ok' => true,
            'message' => 'Payment marked failed.',
        ]);

        $this->assertDatabaseHas('payment_transactions', [
            'id' => $payment->id,
            'status' => PaymentTransaction::STATUS_FAILED,
        ]);
        $this->assertDatabaseHas('ticket_categories', [
            'id' => $ticketCategory->id,
            'tickets_remaining' => 100,
        ]);
        $this->assertDatabaseHas('events', [
            'id' => $event->id,
            'tickets_available' => 100,
        ]);
    }

    public function test_webhook_refund_callback_cancels_ticket(): void
    {
        config(['services.marzepay.webhook_secret' => 'test-secret']);

        $records = $this->makePaymentContext();
        $payment = $records['payment'];
        $event = $records['event'];
        $ticketCategory = $records['ticket_category'];
        $user = $records['user'];

        $ticket = Ticket::query()->create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'ticket_category_id' => $ticketCategory->id,
            'holder_name' => 'Refund Candidate',
            'ticket_code' => 'WADO-' . Str::upper(Str::random(10)),
            'quantity' => 1,
            'unit_price' => 5000,
            'total_amount' => 5000,
            'payment_provider' => 'mtn',
            'status' => Ticket::STATUS_CONFIRMED,
            'purchased_at' => now(),
        ]);

        $payment->forceFill([
            'status' => PaymentTransaction::STATUS_CONFIRMED,
            'ticket_id' => $ticket->id,
            'confirmed_at' => now(),
        ])->save();

        $ticketCategory->forceFill(['tickets_remaining' => 99])->save();
        $event->forceFill(['tickets_available' => 99])->save();

        $payload = [
            'reference' => $payment->idempotency_key,
            'status' => 'refunded',
            'message' => 'Refund processed',
        ];

        $response = $this
            ->withHeader('X-Marze-Signature', $this->signPayload($payload))
            ->postJson(route('payments.webhook.marzepay'), $payload);

        $response->assertOk()->assertJson([
            'ok' => true,
            'message' => 'Payment refunded.',
        ]);

        $this->assertDatabaseHas('payment_transactions', [
            'id' => $payment->id,
            'status' => PaymentTransaction::STATUS_REFUNDED,
        ]);
        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'status' => Ticket::STATUS_CANCELLED,
        ]);
    }

    public function test_duplicate_webhook_returns_ok_without_double_issue_dispatch(): void
    {
        Queue::fake();
        config(['services.marzepay.webhook_secret' => 'test-secret']);

        $records = $this->makePaymentContext();
        $payment = $records['payment'];

        $payload = [
            'reference' => $payment->idempotency_key,
            'status' => 'success',
            'message' => 'Payment complete',
        ];
        $signature = $this->signPayload($payload);

        $first = $this->withHeader('X-Marze-Signature', $signature)
            ->postJson(route('payments.webhook.marzepay'), $payload);
        $second = $this->withHeader('X-Marze-Signature', $signature)
            ->postJson(route('payments.webhook.marzepay'), $payload);

        $first->assertOk()->assertJson(['ok' => true, 'message' => 'Payment confirmed.']);
        $second->assertOk()->assertJson(['ok' => true, 'message' => 'Already confirmed.']);

        Queue::assertPushed(IssueTicketForPayment::class, 1);
    }

    public function test_webhook_confirms_payment_and_dispatches_ticket_job(): void
    {
        Queue::fake();
        config(['services.marzepay.webhook_secret' => 'test-secret']);

        $records = $this->makePaymentContext();
        $payment = $records['payment'];

        $payload = [
            'reference' => $payment->idempotency_key,
            'status' => 'success',
            'message' => 'Payment complete',
        ];

        $raw = json_encode($payload, JSON_UNESCAPED_SLASHES);
        $signature = hash_hmac('sha256', $raw, 'test-secret');

        $response = $this
            ->withHeader('X-Marze-Signature', $signature)
            ->postJson(route('payments.webhook.marzepay'), $payload);

        $response
            ->assertOk()
            ->assertJson([
                'ok' => true,
                'message' => 'Payment confirmed.',
            ]);

        $this->assertDatabaseHas('payment_transactions', [
            'id' => $payment->id,
            'status' => PaymentTransaction::STATUS_CONFIRMED,
        ]);

        Queue::assertPushed(IssueTicketForPayment::class, function (IssueTicketForPayment $job) use ($payment): bool {
            return $job->paymentTransactionId === $payment->id;
        });
    }

    public function test_webhook_rejects_invalid_signature(): void
    {
        config(['services.marzepay.webhook_secret' => 'test-secret']);

        $records = $this->makePaymentContext();
        $payment = $records['payment'];

        $payload = [
            'reference' => $payment->idempotency_key,
            'status' => 'success',
        ];

        $response = $this
            ->withHeader('X-Marze-Signature', 'invalid-signature')
            ->postJson(route('payments.webhook.marzepay'), $payload);

        $response
            ->assertStatus(401)
            ->assertJson([
                'ok' => false,
                'message' => 'Invalid webhook signature.',
            ]);
    }

    /**
     * @return array{payment: PaymentTransaction, event: Event, ticket_category: TicketCategory, user: User}
     */
    protected function makePaymentContext(): array
    {
        $user = User::factory()->create([
            'role' => 'customer',
        ]);

        $category = Category::query()->create([
            'name' => 'Music ' . Str::random(4),
            'slug' => 'music-' . Str::lower(Str::random(6)),
            'description' => 'Category for tests',
        ]);

        $event = Event::query()->create([
            'user_id' => $user->id,
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
            'status' => 'published',
        ]);

        $ticketCategory = TicketCategory::query()->create([
            'event_id' => $event->id,
            'name' => 'Regular',
            'price' => 5000,
            'ticket_count' => 100,
            'tickets_remaining' => 100,
            'sort_order' => 1,
        ]);

        $payment = PaymentTransaction::query()->create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'ticket_category_id' => $ticketCategory->id,
            'holder_name' => 'John Buyer',
            'idempotency_key' => strtoupper((string) Str::uuid()),
            'payment_provider' => 'mtn',
            'phone_number' => '+256771234567',
            'quantity' => 1,
            'unit_price' => 5000,
            'total_amount' => 5000,
            'currency' => 'UGX',
            'status' => PaymentTransaction::STATUS_INITIATED,
            'expires_at' => now()->addMinutes(5),
        ]);

        return [
            'payment' => $payment,
            'event' => $event,
            'ticket_category' => $ticketCategory,
            'user' => $user,
        ];
    }

    protected function signPayload(array $payload): string
    {
        $raw = json_encode($payload, JSON_UNESCAPED_SLASHES);

        return hash_hmac('sha256', (string) $raw, 'test-secret');
    }
}
