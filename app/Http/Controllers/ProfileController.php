<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        return view('pages.profile.show', [
            'user' => $request->user(),
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $normalizedPhone = preg_replace('/(?!^)\+|[^\d+]/', '', trim((string) $request->input('phone')));
        if (str_starts_with((string) $normalizedPhone, '00')) {
            $normalizedPhone = '+' . substr((string) $normalizedPhone, 2);
        }
        $normalizedPhone = blank($normalizedPhone) ? null : $normalizedPhone;

        $request->merge([
            'name' => trim((string) $request->input('name')),
            'email' => Str::lower(trim((string) $request->input('email'))),
            'phone' => $normalizedPhone,
        ]);

        $data = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email:rfc,dns', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'regex:/^\+?[1-9]\d{7,14}$/'],
        ], [
            'email.email' => 'Please enter a valid email address with a real domain.',
            'phone.regex' => 'Please enter a valid phone number in international format, for example +256700000000.',
        ]);

        $user->forceFill([
            'name'  => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
        ])->save();

        return back()->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'current_password' => ['required', 'string', 'current_password'],
            'password'         => ['required', 'confirmed', 'max:72', Password::min(8)->mixedCase()->numbers()],
        ]);

        $user->forceFill([
            'password' => Hash::make($request->password),
        ])->save();

        Auth::logoutOtherDevices($request->password);
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Password changed. Please log in with your new password.');
    }
}
