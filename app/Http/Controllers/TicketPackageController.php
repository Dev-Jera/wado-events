<?php

namespace App\Http\Controllers;

use App\Models\Enquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TicketPackageController extends Controller
{
    public function index()
    {
        $settingsPath = storage_path('app/site-settings.json');
        $s = file_exists($settingsPath)
            ? (json_decode(file_get_contents($settingsPath), true) ?? [])
            : [];

        $defaultPackages = [
            [
                'image' => asset('images/wrist-ticket.jpg'),
                'label' => 'VIP Wristband Tickets',
                'title' => 'Give your VIP guests a premium entry experience',
                'copy'  => 'With our printed VIP wristbands, give your top-tier guests a cleaner, faster access experience from the moment they arrive. Colour-coded per category, tamper-proof, and printed on-demand.',
                'price' => '',
            ],
            [
                'image' => asset('images/cutout-ticket.jpg'),
                'label' => 'Gate-Sale Ticket Printing',
                'title' => 'Print ticket batches for fast sales at the entrance',
                'copy'  => 'Generate tickets in bulk and sell them at entry with optional scanner support when you need more control. Perfect for walk-in audiences and last-minute sales without the tech overhead.',
                'price' => '',
            ],
            [
                'image' => asset('images/Online ticket.jpg'),
                'label' => 'Online Ticketing & Event Management',
                'title' => 'Sell online and let us manage your event',
                'copy'  => 'Let customers buy tickets online while our team manages verification, attendance, and event flow from one organised system. Real-time dashboards, QR scanning, and refund handling included.',
                'price' => '',
            ],
        ];

        $packages = isset($s['packages']) && count($s['packages'])
            ? array_map(fn ($p) => [
                'image' => !empty($p['image']) ? Storage::disk('public')->url($p['image']) : null,
                'label' => $p['label'] ?? '',
                'title' => $p['title'] ?? '',
                'copy'  => $p['copy']  ?? '',
                'price' => $p['price'] ?? '',
            ], $s['packages'])
            : $defaultPackages;

        return view('ticket-packages.index', compact('packages'));
    }

    protected function sendEnquiryNotification(Enquiry $enquiry, array $data): void
    {
        $apiKey = (string) config('services.brevo.api_key', '');
        if ($apiKey === '') {
            Log::warning('PackageEnquiry: BREVO_API_KEY not set, skipping admin notification', [
                'enquiry_id' => $enquiry->id,
            ]);
            return;
        }

        try {
            $html = view('emails.package-enquiry', ['data' => $data])->render();

            $response = Http::timeout(15)
                ->withHeaders([
                    'api-key'      => $apiKey,
                    'Content-Type' => 'application/json',
                    'Accept'       => 'application/json',
                ])
                ->post('https://api.brevo.com/v3/smtp/email', [
                    'sender'      => [
                        'name'  => 'WADO Enquiry Form',
                        'email' => config('mail.from.address', 'wadoconcepts@gmail.com'),
                    ],
                    'to'          => [['email' => 'wadoconcepts@gmail.com']],
                    'cc'          => [['email' => 'aloyobrendaojera@gmail.com']],
                    'replyTo'     => ['email' => $data['email'], 'name' => $data['name']],
                    'subject'     => 'New Package Enquiry — ' . $data['package'],
                    'htmlContent' => $html,
                ]);

            if (! $response->successful()) {
                Log::error('PackageEnquiry: Brevo API error', [
                    'enquiry_id' => $enquiry->id,
                    'status'     => $response->status(),
                    'body'       => $response->body(),
                ]);
            } else {
                Log::info('PackageEnquiry: admin notification sent', ['enquiry_id' => $enquiry->id]);
            }
        } catch (\Throwable $e) {
            Log::error('PackageEnquiry: failed to send admin notification', [
                'enquiry_id' => $enquiry->id,
                'error'      => $e->getMessage(),
            ]);
        }
    }

    public function enquire(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:100',
            'email'      => 'required|email|max:150',
            'phone'      => 'nullable|string|max:30',
            'package'    => 'required|string|max:100',
            'event_date' => 'nullable|date',
            'attendance' => 'nullable|string|max:50',
            'message'    => 'nullable|string|max:1000',
        ]);

        $enquiry = Enquiry::create($validated);

        $this->sendEnquiryNotification($enquiry, $validated);

        return response()->json(['success' => true]);
    }
}
