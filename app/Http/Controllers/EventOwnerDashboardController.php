<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class EventOwnerDashboardController extends Controller
{
    /**
     * Show event owner dashboard login page
     */
    public function showLogin(string $eventSlug)
    {
        $event = Event::whereRaw("LOWER(REPLACE(title, ' ', '-')) = ?", [strtolower($eventSlug)])
            ->orWhere('slug', $eventSlug)
            ->firstOrFail();

        return view('event-owner.login', compact('event', 'eventSlug'));
    }

    /**
     * Handle event owner login
     */
    public function login(Request $request, string $eventSlug)
    {
        $request->merge([
            'email' => Str::lower(trim((string) $request->input('email'))),
        ]);

        $request->validate([
            'email' => 'required|email:rfc,dns|max:255',
            'password' => 'required|string',
        ], [
            'email.required' => 'Email is required',
            'email.email' => 'Please enter a valid email address with a real domain.',
            'password.required' => 'Password is required',
        ]);

        $event = Event::whereRaw("LOWER(REPLACE(title, ' ', '-')) = ?", [strtolower($eventSlug)])
            ->orWhere('slug', $eventSlug)
            ->firstOrFail();

        $credentials = [
            'event_id' => $event->id,
            'email' => Str::lower(trim($request->string('email')->toString())),
            'password' => $request->string('password')->toString(),
            'is_active' => true,
        ];

        if (! Auth::guard('event_owner')->attempt($credentials)) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['credentials' => 'Invalid email or password.']);
        }

        /** @var \App\Models\EventOwnerDashboardAccount $account */
        $account = Auth::guard('event_owner')->user();

        // Update last login
        $account->update(['last_login_at' => now()]);

        // Bridge into Filament admin guard using linked user after owner auth succeeds.
        Auth::guard('admin')->loginUsingId($account->user_id);

        // Create session for event owner
        Session::put('owner_event_slug', $eventSlug);
        Session::put('owner_event_id', $event->id);
        Session::put('owner_id', $account->user_id);
        Session::put('owner_dashboard_account_id', $account->id);

        return redirect('/dashboard')->with('success', 'Welcome to your event dashboard!');
    }

    /**
     * Logout event owner
     */
    public function logout(Request $request)
    {
        Auth::guard('event_owner')->logout();
        Auth::guard('admin')->logout();

        Session::forget(['owner_event_slug', 'owner_event_id', 'owner_id', 'owner_dashboard_account_id']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Logged out successfully.');
    }
}
