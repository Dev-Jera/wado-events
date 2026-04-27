<?php

namespace App\Http\Controllers;

use App\Mail\PackageEnquiry;
use App\Models\Enquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
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

        try {
            Mail::to('wadoconcepts@gmail.com')
                ->cc('aloyobrendaojera@gmail.com')
                ->send(new PackageEnquiry($validated));
        } catch (\Throwable $e) {
            Log::error('PackageEnquiry: failed to send admin notification email', [
                'enquiry_id' => $enquiry->id,
                'error'      => $e->getMessage(),
            ]);
        }

        return response()->json(['success' => true]);
    }
}
