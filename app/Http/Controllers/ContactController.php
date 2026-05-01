<?php

namespace App\Http\Controllers;

use App\Models\Enquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    public function index()
    {
        return view('pages.contact');
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:100',
            'email'   => 'required|email|max:150',
            'phone'   => 'nullable|string|max:30',
            'package' => 'required|string|max:100',
            'message' => 'required|string|max:2000',
        ]);

        $enquiry = Enquiry::create($validated);

        $this->notify($enquiry, $validated);

        return response()->json(['success' => true]);
    }

    private function notify(Enquiry $enquiry, array $data): void
    {
        $apiKey = (string) config('services.brevo.api_key', '');
        if ($apiKey === '') {
            Log::warning('Contact: BREVO_API_KEY not set', ['enquiry_id' => $enquiry->id]);
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

            Http::timeout(15)
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
                    'subject'     => 'Contact: ' . $data['package'] . ' — ' . $data['name'],
                    'htmlContent' => $html,
                ]);
        } catch (\Throwable $e) {
            Log::error('Contact: failed to send notification', ['enquiry_id' => $enquiry->id, 'error' => $e->getMessage()]);
        }
    }
}
