<?php

namespace App\Http\Controllers;

use App\Models\LoginAttempt;
use App\Models\User;
use App\Services\Admin\AdminIncidentNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $this->ensureIsNotRateLimited($request);

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey($request), 60);

            $attempt = $this->recordLoginAttempt($request, false, 'Invalid credentials');

            // Alert admins after 10 failures from the same IP in 10 minutes
            $failureCount = LoginAttempt::recentFailureCountForIp((string) $request->ip(), 10);
            if ($failureCount >= 10 && $failureCount % 10 === 0) {
                app(AdminIncidentNotificationService::class)
                    ->notifySuspiciousLoginActivity($attempt, $failureCount);
            }

            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => 'The provided login details do not match our records.',
                ]);
        }

        RateLimiter::clear($this->throttleKey($request));

        $this->recordLoginAttempt($request, true);

        $request->session()->regenerate();

        return redirect()->to($this->resolvePostAuthRedirect($request));
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => $data['password'],
            'role' => 'customer',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->to($this->resolvePostAuthRedirect($request));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    protected function resolvePostAuthRedirect(Request $request): string
    {
        if ($checkoutRedirect = $request->session()->pull('checkout_redirect')) {
            return $checkoutRedirect;
        }

        $user = $request->user();
        if ($user?->canAccessOperationsPanel()) {
            return url('/dashboard');
        }

        return route('tickets.index');
    }

    protected function recordLoginAttempt(Request $request, bool $succeeded, ?string $failureReason = null): LoginAttempt
    {
        return LoginAttempt::create([
            'email'          => strtolower(trim((string) $request->input('email'))),
            'ip_address'     => (string) $request->ip(),
            'succeeded'      => $succeeded,
            'user_agent'     => $request->userAgent(),
            'user_id'        => $succeeded ? Auth::id() : null,
            'failure_reason' => $failureReason,
            'attempted_at'   => now(),
        ]);
    }

    protected function ensureIsNotRateLimited(Request $request): void
    {
        $key = $this->throttleKey($request);

        if (! RateLimiter::tooManyAttempts($key, 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($key);

        throw ValidationException::withMessages([
            'email' => 'Too many login attempts. Try again in ' . max($seconds, 1) . ' seconds.',
        ]);
    }

    protected function throttleKey(Request $request): string
    {
        return Str::transliterate(Str::lower((string) $request->input('email')) . '|' . $request->ip());
    }
}
