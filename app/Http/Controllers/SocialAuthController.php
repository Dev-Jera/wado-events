<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class SocialAuthController extends Controller
{
    public function redirect(string $provider): RedirectResponse
    {
        abort_unless(in_array($provider, ['google', 'facebook'], true), 404);

        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider): RedirectResponse
    {
        abort_unless(in_array($provider, ['google', 'facebook'], true), 404);

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (Throwable) {
            return redirect()->route('login')->withErrors(['email' => 'Social login failed. Please try again.']);
        }

        $email = (string) ($socialUser->getEmail() ?? '');

        if ($email === '') {
            return redirect()->route('login')->withErrors(['email' => 'Could not retrieve your email from ' . ucfirst($provider) . '. Please log in with email and password.']);
        }

        $user = User::query()
            ->where('email', $email)
            ->orWhere(fn ($q) => $q->where('provider', $provider)->where('provider_id', (string) $socialUser->getId()))
            ->first();

        if ($user) {
            // Update provider info if they logged in with email before
            if (! $user->provider) {
                $user->forceFill([
                    'provider'    => $provider,
                    'provider_id' => (string) $socialUser->getId(),
                ])->save();
            }
        } else {
            $user = User::create([
                'name'        => $socialUser->getName() ?: $email,
                'email'       => $email,
                'provider'    => $provider,
                'provider_id' => (string) $socialUser->getId(),
                'role'        => 'customer',
                'password'    => null,
            ]);
        }

        Auth::login($user, remember: true);
        request()->session()->regenerate();

        return redirect()->intended(route('tickets.index'));
    }
}
