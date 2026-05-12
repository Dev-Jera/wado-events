<?php

namespace App\Services\Event;

use App\Models\Event;
use App\Models\EmailLog;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EventFulfilmentNotificationService
{
    /**
     * Notify a physical-package event owner that fulfilment is ready and include next-step instructions.
     */
    public function sendReadyForDelivery(Event $event): void
    {
        $event->loadMissing(['user', 'category']);

        /** @var User|null $owner */
        $owner = $event->user;
        $subject = 'Your physical tickets are ready - next steps';

        if (! $owner instanceof User || blank($owner->email)) {
            Log::warning('Fulfilment ready email skipped: missing owner email.', [
                'event_id' => $event->id,
            ]);

            EmailLog::create([
                'recipient' => (string) ($owner?->email ?? ''),
                'subject' => $subject,
                'source' => 'event.fulfilment_ready',
                'status' => 'failed',
                'error' => 'Missing owner email.',
            ]);

            return;
        }

        if (! in_array((string) $event->service_package, ['batch_tickets', 'premium_wristbands'], true)) {
            return;
        }

        $apiKey = (string) config('services.brevo.api_key', '');

        if ($apiKey === '') {
            Log::warning('Fulfilment ready email skipped: BREVO_API_KEY not configured.', [
                'event_id' => $event->id,
                'owner_id' => $owner->id,
            ]);

            EmailLog::create([
                'recipient' => (string) $owner->email,
                'subject' => $subject,
                'source' => 'event.fulfilment_ready',
                'status' => 'failed',
                'error' => 'BREVO_API_KEY not configured.',
            ]);

            return;
        }

        $slug = \Illuminate\Support\Str::slug((string) $event->title);
        $dashboardLoginUrl = route('owner.dashboard-access', ['eventSlug' => $slug], false);
        $contactUrl = route('contact');

        $html = view('emails.events.fulfilment-ready-owner-next-steps', [
            'event' => $event,
            'owner' => $owner,
            'dashboardLoginUrl' => $dashboardLoginUrl,
            'contactUrl' => $contactUrl,
        ])->render();

        try {
            $response = Http::timeout(20)
                ->withHeaders([
                    'api-key' => $apiKey,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->post('https://api.brevo.com/v3/smtp/email', [
                    'sender' => [
                        'name' => 'WADO Events',
                        'email' => (string) config('mail.from.address', 'noreply@wado-events.com'),
                    ],
                    'to' => [[
                        'email' => (string) $owner->email,
                        'name' => (string) $owner->name,
                    ]],
                    'subject' => $subject,
                    'htmlContent' => $html,
                ]);

            if (! $response->successful()) {
                throw new \RuntimeException('Brevo API error ' . $response->status() . ': ' . $response->body());
            }

            Log::info('Fulfilment ready email sent.', [
                'event_id' => $event->id,
                'owner_id' => $owner->id,
                'recipient' => $owner->email,
            ]);

            EmailLog::create([
                'recipient' => (string) $owner->email,
                'subject' => $subject,
                'source' => 'event.fulfilment_ready',
                'status' => 'sent',
            ]);
        } catch (\Throwable $e) {
            Log::warning('Fulfilment ready email failed.', [
                'event_id' => $event->id,
                'owner_id' => $owner->id,
                'error' => $e->getMessage(),
            ]);

            EmailLog::create([
                'recipient' => (string) $owner->email,
                'subject' => $subject,
                'source' => 'event.fulfilment_ready',
                'status' => 'failed',
                'error' => $e->getMessage(),
            ]);
        }
    }
}
