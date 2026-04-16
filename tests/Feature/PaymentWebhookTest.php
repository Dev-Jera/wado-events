<?php

namespace Tests\Feature;

use App\Jobs\IssueTicketForPayment;
use App\Models\Category;
use App\Models\Event;
use App\Models\PaymentTransaction;
use App\Models\TicketCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Tests\TestCase;

class PaymentWebhookTest extends TestCase
{
    use RefreshDatabase;

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
     * @return array{payment: PaymentTransaction}
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
        ];
    }
}
