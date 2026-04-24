<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TicketPackageController extends Controller
{
    public function index()
    {
        $ticketPackages = [
            [
                'name' => 'Standard Package',
                'price' => 50,
                'description' => 'Access to all general events.',
            ],
            [
                'name' => 'VIP Package',
                'price' => 150,
                'description' => 'Includes VIP seating and complimentary drinks.',
            ],
            [
                'name' => 'Family Package',
                'price' => 120,
                'description' => 'Discounted access for families (up to 4 members).',
            ],
        ];

        return view('ticket-packages.index', compact('ticketPackages'));
    }
}