<?php

namespace App\Http\Controllers;

use App\Models\LoginAttempt;
use App\Models\User;
use App\Services\Admin\AdminIncidentNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/**
 * @group Authentication
 *
 * Login, registration, and logout.
 */
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
        if ($request->filled('website')) {
            return redirect()->route('home');
        }

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

        $user->sendEmailVerificationNotification();

        return redirect()->to($this->resolvePostAuthRedirect($request));
    }

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);

        Password::sendResetLink($request->only('email'));

        // Always return the same message regardless of whether the email exists
        // — prevents attackers from enumerating which emails are registered.
        return back()->with('status', 'If that email is registered, you\'ll receive a reset link shortly.');
    }

    public function showResetPassword(string $token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'    => ['required'],
            'email'    => ['required', 'email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password): void {
                $user->forceFill(['password' => $password])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', 'Your password has been reset. You can now log in.')
            : back()->withErrors(['email' => __($status)]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    public function verifyNotice(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('tickets.index');
        }

        return view('auth.verify-email');
    }

    public function verifyEmail(Request $request, string $id, string $hash)
    {
        $user = User::findOrFail($id);

        abort_unless(hash_equals(sha1($user->getEmailForVerification()), $hash), 403);

        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        Auth::login($user);

        return redirect()->route('tickets.index')->with('status', 'Email verified! You\'re all set.');
    }

    public function resendVerification(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return back()->with('status', 'Your email is already verified.');
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'Verification link sent — check your inbox.');
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
