<?php

namespace App\Http\Controllers;

use App\Models\EmailLog;
use App\Models\Enquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ContactController extends Controller
{
    public function index()
    {
        return view('pages.contact');
    }

    public function send(Request $request)
    {
        $normalizedPhone = preg_replace('/(?!^)\+|[^\d+]/', '', trim((string) $request->input('phone')));
        if (str_starts_with((string) $normalizedPhone, '00')) {
            $normalizedPhone = '+' . substr((string) $normalizedPhone, 2);
        }
        $normalizedPhone = blank($normalizedPhone) ? null : $normalizedPhone;

        $request->merge([
            'name' => trim((string) $request->input('name')),
            'email' => Str::lower(trim((string) $request->input('email'))),
            'phone' => $normalizedPhone,
            'package' => trim((string) $request->input('package')),
            'message' => trim((string) $request->input('message')),
        ]);

        $validated = $request->validate([
            'name'    => 'required|string|max:100',
            'email'   => 'required|email:rfc,dns|max:150',
            'phone'   => ['nullable', 'regex:/^\+?[1-9]\d{7,14}$/'],
            'package' => 'required|string|max:100',
            'message' => 'required|string|max:2000',
        ], [
            'email.email' => 'Please enter a valid email address with a real domain.',
            'phone.regex' => 'Please enter a valid phone number in international format, for example +256700000000.',
        ]);

        $enquiry = Enquiry::create($validated);

        $this->notify($enquiry, $validated);

        return response()->json(['success' => true]);
    }

    private function notify(Enquiry $enquiry, array $data): void
    {
        $recipient = 'wadoconcepts@gmail.com, aloyobrendaojera@gmail.com';
        $subject = 'Contact: ' . $data['package'] . ' — ' . $data['name'];

        $apiKey = (string) config('services.brevo.api_key', '');
        if ($apiKey === '') {
            Log::warning('Contact: BREVO_API_KEY not set', ['enquiry_id' => $enquiry->id]);

            EmailLog::create([
                'recipient' => $recipient,
                'subject' => $subject,
                'source' => 'contact.enquiry',
                'status' => 'failed',
                'error' => 'BREVO_API_KEY not configured.',
            ]);

            return;
        }

        try {
            $html = '
                <h2 style="color:#c0283c">New Contact Enquiry</h2>
                <p><strong>Type:</strong> ' . e($data['package']) . '</p>
                <p><strong>Name:</strong> ' . e($data['name']) . '</p>
                <p><strong>Email:</strong> ' . e($data['email']) . '</p>
                <p><strong>Phone:</strong> ' . e($data['phone'] ?? 'Not provided') . '</p>
                <p><strong>Message:</strong></p>
                <p style="background:#f9f9f9;padding:12px;border-radius:6px">' . nl2br(e($data['message'])) . '</p>
            ';

            $response = Http::timeout(15)
                ->withHeaders([
                    'api-key'      => $apiKey,
                    'Content-Type' => 'application/json',
                    'Accept'       => 'application/json',
                ])
                ->post('https://api.brevo.com/v3/smtp/email', [
                    'sender'      => ['name' => 'WADO Contact Form', 'email' => config('mail.from.address', 'wadoconcepts@gmail.com')],
                    'to'          => [['email' => 'wadoconcepts@gmail.com']],
                    'cc'          => [['email' => 'aloyobrendaojera@gmail.com']],
                    'replyTo'     => ['email' => $data['email'], 'name' => $data['name']],
                    'subject'     => $subject,
                    'htmlContent' => $html,
                ]);

            if (! $response->successful()) {
                throw new \RuntimeException('Brevo API error ' . $response->status() . ': ' . $response->body());
            }

            EmailLog::create([
                'recipient' => $recipient,
                'subject' => $subject,
                'source' => 'contact.enquiry',
                'status' => 'sent',
            ]);
        } catch (\Throwable $e) {
            Log::error('Contact: failed to send notification', ['enquiry_id' => $enquiry->id, 'error' => $e->getMessage()]);

            EmailLog::create([
                'recipient' => $recipient,
                'subject' => $subject,
                'source' => 'contact.enquiry',
                'status' => 'failed',
                'error' => $e->getMessage(),
            ]);
        }
    }
}
