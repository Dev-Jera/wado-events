<?php

namespace App\Services\Payment;

use App\Models\EmailLog;
use App\Models\PaymentTransaction;
use App\Models\Ticket;
use Barryvdh\DomPDF\Facade\Pdf;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PaymentNotificationService
{
    public function sendTicketConfirmed(Ticket $ticket, PaymentTransaction $payment): void
    {
        $this->sendEmail($ticket, $payment);
        $this->sendSms($ticket, $payment);
    }

    public function sendFreeTicketConfirmed(Ticket $ticket): void
    {
        $this->sendEmail($ticket, null);
    }

    public function sendEmail(Ticket $ticket, ?PaymentTransaction $payment): void
    {
        $recipient = '';
        $subject   = 'Ticket Confirmation';

        try {
            $recipient = (string) ($ticket->user?->email ?? '');
            if ($recipient === '') {
                Log::warning('TicketConfirmed: no recipient email', ['ticket_id' => $ticket->id]);
                return;
            }

            $ticket    = $ticket->fresh(['event', 'user', 'ticketCategory']);
            $ticketUrl = route('tickets.show', $ticket);
            $subject   = 'Your WADO Ticket — ' . ($ticket->event?->title ?? 'Event Confirmed');

            // Generate PNG QR code for email (SVG data URIs are blocked by Gmail)
            $qrCodeDataUri = null;
            try {
                $qrPayload = json_encode([
                    'v'        => 2,
                    'code'     => (string) $ticket->ticket_code,
                    'event_id' => (int) $ticket->event_id,
                ], JSON_UNESCAPED_SLASHES);

                $qrResult = (new Builder(
                    writer: new PngWriter(),
                    writerOptions: [],
                    data: $qrPayload,
                    encoding: new Encoding('UTF-8'),
                    errorCorrectionLevel: ErrorCorrectionLevel::Medium,
                    size: 300,
                    margin: 10,
                    roundBlockSizeMode: RoundBlockSizeMode::Margin,
                ))->build();

                $qrCodeDataUri = 'data:image/png;base64,' . base64_encode($qrResult->getString());
            } catch (\Throwable $qrEx) {
                Log::warning('TicketConfirmed: QR generation failed for email', [
                    'ticket_id' => $ticket->id,
                    'error'     => $qrEx->getMessage(),
                ]);
            }

            // Render email HTML
            $htmlContent = view('emails.tickets.confirmed', [
                'ticket'        => $ticket,
                'payment'       => $payment,
                'ticketUrl'     => $ticketUrl,
                'qrCodeDataUri' => $qrCodeDataUri,
            ])->render();

            // Build Brevo API payload
            $payload = [
                'sender'      => [
                    'name'  => config('mail.from.name', config('app.name')),
                    'email' => config('mail.from.address', 'hello@example.com'),
                ],
                'to'          => [['email' => $recipient]],
                'subject'     => $subject,
                'htmlContent' => $htmlContent,
            ];

            // Attach PDF ticket
            try {
                $pdf = Pdf::loadView('pages.tickets.pdf_compact', [
                    'ticket'        => $ticket,
                    'qrCodeDataUri' => $qrCodeDataUri,
                ]);
                $pdf->setPaper('a4', 'portrait');
                $pdf->setOptions([
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled'      => false,
                    'defaultFont'          => 'sans-serif',
                ]);
                $payload['attachment'] = [[
                    'name'    => 'wado-ticket-' . $ticket->ticket_code . '.pdf',
                    'content' => base64_encode($pdf->output()),
                ]];
            } catch (\Throwable $pdfEx) {
                Log::warning('TicketConfirmed: PDF generation failed, sending without attachment', [
                    'ticket_id' => $ticket->id,
                    'error'     => $pdfEx->getMessage(),
                ]);
            }

            $apiKey = (string) config('services.brevo.api_key', '');
            if ($apiKey === '') {
                throw new \RuntimeException('Brevo API key not configured (BREVO_API_KEY)');
            }

            $response = Http::timeout(30)
                ->withHeaders([
                    'api-key'      => $apiKey,
                    'Content-Type' => 'application/json',
                    'Accept'       => 'application/json',
                ])
                ->post('https://api.brevo.com/v3/smtp/email', $payload);

            if (! $response->successful()) {
                throw new \RuntimeException(
                    'Brevo API error ' . $response->status() . ': ' . $response->body()
                );
            }

            EmailLog::create([
                'ticket_id' => $ticket->id,
                'recipient' => $recipient,
                'subject'   => $subject,
                'status'    => 'sent',
            ]);

            Log::info('TicketConfirmed: email sent via Brevo API', [
                'ticket_id' => $ticket->id,
                'to'        => $recipient,
            ]);
        } catch (\Throwable $e) {
            Log::error('TicketConfirmed: email notification failed', [
                'ticket_id' => $ticket->id ?? null,
                'error'     => $e->getMessage(),
            ]);

            EmailLog::create([
                'ticket_id' => $ticket->id ?? null,
                'recipient' => $recipient,
                'subject'   => $subject,
                'status'    => 'failed',
                'error'     => $e->getMessage(),
            ]);
        }
    }

    protected function sendSms(Ticket $ticket, PaymentTransaction $payment): void
    {
        $apiKey = (string) config('services.africas_talking.api_key', '');
        $username = (string) config('services.africas_talking.username', '');
        $from = (string) config('services.africas_talking.from', '');
        $baseUrl = rtrim((string) config('services.africas_talking.base_url', 'https://api.africastalking.com'), '/');

        if ($apiKey === '' || $username === '') {
            Log::info('SMS notification skipped - Africa\'s Talking credentials not configured', [
                'ticket_id' => $ticket->id,
            ]);

            return;
        }

        $rawPhone = (string) ($payment->phone_number ?: ($ticket->user?->phone ?? ''));
        $digits = preg_replace('/\D+/', '', $rawPhone) ?? '';
        if ($digits === '') {
            Log::warning('SMS notification skipped - missing destination phone number', [
                'ticket_id' => $ticket->id,
                'payment_id' => $payment->id,
            ]);

            return;
        }

        $phone = str_starts_with($digits, '256')
            ? ('+' . $digits)
            : ('+256' . ltrim($digits, '0'));

        $message = sprintf(
            'WADO: Payment confirmed. Ticket %s for %s on %s at %s.',
            $ticket->ticket_code,
            (string) ($ticket->event?->title ?? 'your event'),
            (string) ($ticket->event?->starts_at?->format('d M H:i') ?? 'TBD'),
            (string) ($ticket->event?->venue ?? 'venue TBC')
        );

        $payload = [
            'username' => $username,
            'to' => $phone,
            'message' => $message,
        ];

        if ($from !== '') {
            $payload['from'] = $from;
        }

        $response = Http::timeout(20)
            ->asForm()
            ->withHeaders([
                'apiKey' => $apiKey,
                'Accept' => 'application/json',
            ])
            ->post($baseUrl . '/version1/messaging', $payload);

        if (! $response->successful()) {
            Log::error('SMS notification failed', [
                'ticket_id' => $ticket->id,
                'payment_id' => $payment->id,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return;
        }

        Log::info('SMS notification sent', [
            'ticket_id' => $ticket->id,
            'payment_id' => $payment->id,
            'to' => $phone,
        ]);
    }
}
