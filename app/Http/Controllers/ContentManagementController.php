<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContentManagementController extends Controller
{
    public function index()
    {
        // Fetch existing content for the home page
        $content = [
            'hero_title' => 'Discover Unforgettable Events Near You',
            'hero_subtitle' => 'Concerts, sports, workshops & more — book your spot in seconds.',
            'hero_images' => [], // Fetch from database or storage
            'ticket_packages' => '', // Fetch from database
        ];

        return view('admin.content-management', compact('content'));
    }

    public function store(Request $request)
    {
        // Validate and save the content
        $validated = $request->validate([
            'hero_title' => 'required|string|max:255',
            'hero_subtitle' => 'required|string|max:255',
            'hero_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'ticket_packages' => 'nullable|string',
        ]);

        // Save logic here (e.g., save to database or storage)

        return redirect()->route('content-management.index')->with('success', 'Content updated successfully!');
    }
}