@extends('layouts.app')

@section('fullbleed', '1')

@section('content')
    <section class="auth-page">
        <div class="auth-shell auth-shell--narrow">
            <aside class="auth-visual">
                <div class="auth-logo-mark">
                    <img src="{{ asset('images/signup-image.jfif') }}" alt="Wado Tickets">
                </div>
                <a href="{{ route('home') }}" class="auth-logo-name">Wado Tickets</a>
                <h2 class="auth-title"><span>Choose</span><br>a new<br>password</h2>
                <p class="auth-tagline">Pick something strong and memorable</p>
            </aside>

            <div class="auth-card">
                <div class="auth-card-panel">
                    <h1>Reset password</h1>
                    <p class="auth-copy">Enter your email and a new password below.</p>

                    <form method="POST" action="{{ route('password.update') }}" class="auth-form">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">

                        <label>
                            <span>Email address</span>
                            <input type="email" name="email" value="{{ old('email', request()->email) }}" required autofocus>
                            @error('email') <small>{{ $message }}</small> @enderror
                        </label>

                        <label>
                            <span>New password</span>
                            <div class="auth-password-wrap">
                                <input id="reset-password" type="password" name="password" required>
                                <button type="button" class="auth-eye-btn" data-target="reset-password" aria-label="Show password">
                                    <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                                        <path d="M2 12s3.6-6 10-6 10 6 10 6-3.6 6-10 6S2 12 2 12z" fill="none" stroke="currentColor" stroke-width="1.8"/>
                                        <circle cx="12" cy="12" r="2.8" fill="none" stroke="currentColor" stroke-width="1.8"/>
                                        <path class="eye-off" d="M4 20 20 4" fill="none" stroke="currentColor" stroke-width="1.8"/>
                                    </svg>
                                </button>
                            </div>
                            @error('password') <small>{{ $message }}</small> @enderror
                        </label>

                        <label>
                            <span>Confirm new password</span>
                            <div class="auth-password-wrap">
                                <input id="reset-password-confirm" type="password" name="password_confirmation" required>
                                <button type="button" class="auth-eye-btn" data-target="reset-password-confirm" aria-label="Show confirmation">
                                    <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                                        <path d="M2 12s3.6-6 10-6 10 6 10 6-3.6 6-10 6S2 12 2 12z" fill="none" stroke="currentColor" stroke-width="1.8"/>
                                        <circle cx="12" cy="12" r="2.8" fill="none" stroke="currentColor" stroke-width="1.8"/>
                                        <path class="eye-off" d="M4 20 20 4" fill="none" stroke="currentColor" stroke-width="1.8"/>
                                    </svg>
                                </button>
                            </div>
                        </label>

                        <button type="submit" class="auth-btn">Set new password</button>
                    </form>

                    <p class="auth-switch">Remembered it? <a href="{{ route('login') }}">Back to login</a></p>
                </div>
            </div>
        </div>
    </section>

    <style>
        :root {
            --brand-blue: #1a73e8;
            --brand-blue-dark: #1558c0;
            --brand-red: #e8241a;
            --brand-red-dark: #c01e15;
        }

        .auth-page { min-height: 100vh; background: #150508; }

        .auth-shell { min-height: 100vh; width: min(1120px, 100%); margin: 0 auto; display: grid; grid-template-columns: 0.96fr 1.04fr; }
        .auth-shell--narrow { grid-template-columns: 1fr 1fr; max-width: 860px; }

        .auth-visual { background: #1a0508; padding: clamp(6rem, 15vh, 8.4rem) 0.9rem 2rem 1rem; display: flex; flex-direction: column; align-items: flex-end; justify-content: flex-start; text-align: left; }

        .auth-logo-mark { width: 58px; height: 58px; border-radius: 14px; background: var(--brand-blue); display: grid; place-items: center; overflow: hidden; box-shadow: 0 10px 24px rgba(192,40,60,0.25); }
        .auth-logo-mark img { width: 38px; height: 38px; object-fit: contain; }

        .auth-logo-name { margin-top: 0.85rem; color: var(--brand-blue); font-size: 1.05rem; font-weight: 900; text-decoration: none; width: min(360px, 100%); }

        .auth-title { margin: 1.4rem 0 0; color: #fff; font-size: clamp(2.2rem, 4.2vw, 3.5rem); line-height: 1.02; font-weight: 800; width: min(360px, 100%); }
        .auth-title span { color: var(--brand-blue); }

        .auth-tagline { margin: 1rem 0 0; color: #8a9ab8; font-size: 1rem; width: min(360px, 100%); }

        .auth-card { background: #1a0508; padding: 2rem 0.6rem 2rem 0; display: flex; align-items: center; justify-content: flex-start; }

        .auth-card-panel { width: min(405px, 100%); margin-left: -10px; background: #1e0b0e; border: 1px solid #3a1520; border-radius: 20px; padding: 1.5rem 1.35rem 1.2rem; box-shadow: 0 12px 30px rgba(0,0,0,0.4); }

        .auth-card h1 { margin: 0; color: #fff; font-size: clamp(1.55rem, 2.2vw, 1.85rem); font-weight: 800; text-align: center; }

        .auth-copy { margin: 0.6rem 0 1.1rem; color: #8a9ab8; font-size: 0.9rem; line-height: 1.42; text-align: center; }

        .auth-form { display: grid; gap: 0.85rem; }
        .auth-form label { display: grid; gap: 0.4rem; color: #c0cfe8; font-size: 0.86rem; font-weight: 700; }
        .auth-form input[type="email"],
        .auth-form input[type="password"] { width: 100%; height: 42px; border: 1px solid #3a1520; border-radius: 8px; background: #150508; color: #fff; padding: 0 0.8rem; font-size: 0.9rem; transition: border-color 0.15s, box-shadow 0.15s; }
        .auth-form input:focus { outline: none; border-color: var(--brand-blue); box-shadow: 0 0 0 3px rgba(192,40,60,0.12); }
        .auth-form small { color: var(--brand-red); font-size: 0.75rem; font-weight: 600; }

        .auth-password-wrap { position: relative; }
        .auth-password-wrap input { padding-right: 2.5rem; }
        .auth-eye-btn { position: absolute; right: 0.55rem; top: 50%; transform: translateY(-50%); width: 28px; height: 28px; border: 0; border-radius: 6px; background: transparent; color: var(--brand-blue); cursor: pointer; display: grid; place-items: center; }
        .auth-eye-btn svg { width: 18px; height: 18px; }
        .auth-eye-btn .eye-off { opacity: 1; }
        .auth-eye-btn.is-visible .eye-off { opacity: 0; }

        .auth-btn { height: 44px; border: none; border-radius: 8px; background: var(--brand-red); color: #fff; font-size: 0.92rem; font-weight: 700; cursor: pointer; transition: background 0.15s; width: 100%; }
        .auth-btn:hover { background: var(--brand-red-dark); }

        .auth-switch { margin: 0.95rem 0 0; text-align: center; color: #8a9ab8; font-size: 0.92rem; }
        .auth-switch a { color: var(--brand-blue); font-weight: 700; text-decoration: none; }

        @media (max-width: 860px) {
            .auth-shell { grid-template-columns: 1fr; width: 100%; }
            .auth-visual { padding: 3.1rem 1.25rem 1.6rem; align-items: center; text-align: center; }
            .auth-logo-name, .auth-title, .auth-tagline { width: auto; }
            .auth-card { padding: 1.25rem; }
            .auth-card-panel { width: 100%; margin-left: 0; }
        }
    </style>

    <script>
        (function () {
            document.querySelectorAll('.auth-eye-btn').forEach((btn) => {
                btn.addEventListener('click', () => {
                    const input = document.getElementById(btn.getAttribute('data-target'));
                    if (!input) return;
                    const show = input.type === 'password';
                    input.type = show ? 'text' : 'password';
                    btn.classList.toggle('is-visible', show);
                    btn.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
                });
            });
        })();
    </script>
@endsection
