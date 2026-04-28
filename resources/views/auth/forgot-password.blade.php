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
                <h2 class="auth-title"><span>Reset</span><br>your<br>password</h2>
                <p class="auth-tagline">We'll send a secure link to your inbox</p>
            </aside>

            <div class="auth-card">
                <div class="auth-card-panel">
                    <h1>Forgot password?</h1>
                    <p class="auth-copy">Enter your email address and we'll send you a link to reset your password.</p>

                    @if (session('status'))
                        <p class="auth-status-msg">{{ session('status') }}</p>
                    @endif

                    <form method="POST" action="{{ route('password.email') }}" class="auth-form">
                        @csrf

                        <label>
                            <span>Email address</span>
                            <input type="email" name="email" value="{{ old('email') }}" required autofocus>
                            @error('email') <small>{{ $message }}</small> @enderror
                        </label>

                        <button type="submit" class="auth-btn">Send reset link</button>
                    </form>

                    <p class="auth-switch">Remember it? <a href="{{ route('login') }}">Back to login</a></p>
                </div>
            </div>
        </div>
    </section>

    <style>
        :root {
            --brand-blue: #c0283c;
            --brand-blue-dark: #8a1525;
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
        .auth-form input[type="email"] { width: 100%; height: 42px; border: 1px solid #3a1520; border-radius: 8px; background: #150508; color: #fff; padding: 0 0.8rem; font-size: 0.9rem; transition: border-color 0.15s, box-shadow 0.15s; }
        .auth-form input:focus { outline: none; border-color: var(--brand-blue); box-shadow: 0 0 0 3px rgba(192,40,60,0.12); }
        .auth-form small { color: var(--brand-red); font-size: 0.75rem; font-weight: 600; }

        .auth-btn { height: 44px; border: none; border-radius: 8px; background: var(--brand-red); color: #fff; font-size: 0.92rem; font-weight: 700; cursor: pointer; transition: background 0.15s; width: 100%; }
        .auth-btn:hover { background: var(--brand-red-dark); }

        .auth-switch { margin: 0.95rem 0 0; text-align: center; color: #8a9ab8; font-size: 0.92rem; }
        .auth-switch a { color: var(--brand-blue); font-weight: 700; text-decoration: none; }

        .auth-status-msg { margin: 0 0 1rem; padding: 0.65rem 0.85rem; background: rgba(22,163,74,0.12); border: 1px solid rgba(22,163,74,0.3); border-radius: 8px; color: #4ade80; font-size: 0.86rem; line-height: 1.35; }

        @media (max-width: 860px) {
            .auth-shell { grid-template-columns: 1fr; width: 100%; }
            .auth-visual { padding: 3.1rem 1.25rem 1.6rem; align-items: center; text-align: center; }
            .auth-logo-name, .auth-title, .auth-tagline { width: auto; }
            .auth-card { padding: 1.25rem; }
            .auth-card-panel { width: 100%; margin-left: 0; }
        }
    </style>
@endsection
