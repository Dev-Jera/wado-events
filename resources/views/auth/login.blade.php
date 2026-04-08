@extends('layouts.app')

@section('fullbleed', '1')

@section('content')
    <section class="auth-page">
        <div class="auth-shell">
            <aside class="auth-visual">
                <div class="auth-logo-mark">
                    <img src="{{ asset('images/signup-image.jfif') }}" alt="Wado Tickets">
                </div>
                <a href="{{ route('home') }}" class="auth-logo-name">Wado Tickets</a>
                <h2 class="auth-title"><span>Sign In</span><br>account<br>access</h2>
                <p class="auth-tagline">Secure entry for returning users</p>
            </aside>

            <div class="auth-card">
                <div class="auth-card-panel">
                    <h1>Log in to your account</h1>
                    <p class="auth-copy">Sign in once &mdash; complete purchases, return later, and open My Tickets anytime.</p>

                    <form method="POST" action="{{ route('login.store') }}" class="auth-form">
                        @csrf

                        <label>
                            <span>Email address</span>
                            <input type="email" name="email" value="{{ old('email') }}" required autofocus>
                            @error('email') <small>{{ $message }}</small> @enderror
                        </label>

                        <div class="auth-pw-row">
                            <label>
                                <span>Password</span>
                                <div class="auth-password-wrap">
                                    <input id="login-password" type="password" name="password" required>
                                    <button type="button" class="auth-eye-btn" data-target="login-password" aria-label="Show password">
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
                                <span>Keep me signed in</span>
                                <div class="auth-check-box">
                                    <input type="checkbox" name="remember" value="1">
                                </div>
                            </label>
                        </div>

                        <button type="submit" class="auth-btn">Log in</button>
                    </form>

                    <div class="auth-divider"><hr><span>or log in with</span><hr></div>

                    <div class="auth-social">
                        <button type="button" class="auth-social-btn">
                            <span class="auth-social-icon"><img src="{{ asset('images/google.png') }}" alt="Google logo"></span>
                            <span>Google</span>
                        </button>
                        <button type="button" class="auth-social-btn">
                            <span class="auth-social-icon"><img src="{{ asset('images/facebook-logo.png') }}" alt="Facebook logo"></span>
                            <span>Facebook</span>
                        </button>
                    </div>

                    <p class="auth-switch">New here? <a href="{{ route('register') }}">Create an account</a></p>
                </div>

                <div class="auth-art" aria-hidden="true">
                    <div class="auth-art-curve"></div>
                    <span class="auth-art-dot auth-art-dot-top"></span>
                    <span class="auth-art-dot auth-art-dot-bottom"></span>
                    <div class="auth-art-alert">
                        <div class="auth-art-alert-input">************</div>
                        <p>Password is too simple or there are not enough characters in it</p>
                    </div>
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

        .auth-page { min-height: 100vh; background: #07101c; }

        .auth-shell { min-height: 100vh; width: min(1120px, 100%); margin: 0 auto; display: grid; grid-template-columns: 0.96fr 1.04fr; }

        .auth-visual { background: #0a1525; padding: clamp(6rem, 15vh, 8.4rem) 0.9rem 2rem 1rem; display: flex; flex-direction: column; align-items: flex-end; justify-content: flex-start; text-align: left; }

        .auth-logo-mark { width: 58px; height: 58px; border-radius: 14px; background: var(--brand-blue); display: grid; place-items: center; overflow: hidden; box-shadow: 0 10px 24px rgba(26, 115, 232, 0.25); }
        .auth-logo-mark img { width: 38px; height: 38px; object-fit: contain; }

        .auth-logo-name { margin-top: 0.85rem; color: var(--brand-blue); font-size: 1.05rem; font-weight: 900; text-decoration: none; width: min(360px, 100%); }

        .auth-title { margin: 1.4rem 0 0; color: #fff; font-size: clamp(2.2rem, 4.2vw, 3.5rem); line-height: 1.02; font-weight: 800; width: min(360px, 100%); }
        .auth-title span { color: var(--brand-blue); }

        .auth-tagline { margin: 1rem 0 0; color: #8a9ab8; font-size: 1rem; width: min(360px, 100%); }

        .auth-card { background: #0a1525; padding: 2rem 0.6rem 2rem 0; display: flex; align-items: flex-start; justify-content: flex-start; position: relative; }

        .auth-card-panel { width: min(405px, 100%); margin-left: -10px; background: #111d2e; border: 1px solid #1e3050; border-radius: 20px; padding: 1.5rem 1.35rem 1.2rem; box-shadow: 0 12px 30px rgba(0,0,0,0.4); }

        .auth-card h1 { margin: 0; color: #fff; font-size: clamp(1.55rem, 2.2vw, 1.85rem); line-height: 1.08; font-weight: 800; text-align: center; }

        .auth-copy { margin: 0.6rem 0 1.1rem; color: #8a9ab8; font-size: 0.9rem; line-height: 1.42; text-align: center; }

        .auth-form { display: grid; gap: 0.85rem; }
        .auth-form label { display: grid; gap: 0.4rem; color: #c0cfe8; font-size: 0.86rem; font-weight: 700; }

        .auth-form input[type="email"],
        .auth-form input[type="password"] { width: 100%; height: 42px; border: 1px solid #1e3050; border-radius: 8px; background: #0d1929; color: #fff; padding: 0 0.8rem; font-size: 0.9rem; transition: border-color 0.15s, box-shadow 0.15s; }

        .auth-form input:focus { outline: none; border-color: var(--brand-blue); box-shadow: 0 0 0 3px rgba(26, 115, 232, 0.12); }

        .auth-form small { color: var(--brand-red); font-size: 0.75rem; font-weight: 600; }

        .auth-pw-row { display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 0.8rem; align-items: end; }

        .auth-password-wrap { position: relative; }
        .auth-password-wrap input { padding-right: 2.5rem; }

        .auth-eye-btn { position: absolute; right: 0.55rem; top: 50%; transform: translateY(-50%); width: 28px; height: 28px; border: 0; border-radius: 6px; background: transparent; color: var(--brand-blue); cursor: pointer; display: grid; place-items: center; }
        .auth-eye-btn svg { width: 18px; height: 18px; }
        .auth-eye-btn .eye-off { opacity: 1; }
        .auth-eye-btn.is-visible .eye-off { opacity: 0; }

        .auth-check-box { height: 42px; border-radius: 8px; border: 1px solid #1e3050; background: #0d1929; display: flex; align-items: center; justify-content: center; }
        .auth-check-box input { width: 18px; height: 18px; accent-color: var(--brand-blue); }

        .auth-btn { height: 44px; border: none; border-radius: 8px; background: var(--brand-red); color: #fff; font-size: 0.92rem; font-weight: 700; cursor: pointer; transition: background 0.15s; }
        .auth-btn:hover { background: var(--brand-red-dark); }

        .auth-divider { display: flex; align-items: center; gap: 0.8rem; margin: 1rem 0; }
        .auth-divider hr { flex: 1; border: none; border-top: 1px solid #1e3050; }
        .auth-divider span { color: #8a9ab8; font-size: 0.86rem; }

        .auth-social { display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; }
        .auth-social-btn { height: 42px; border-radius: 8px; border: 1px solid #1e3050; background: #0d1929; color: #c0cfe8; font-size: 0.88rem; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.5rem; transition: border-color 0.15s; }
        .auth-social-btn:hover { border-color: var(--brand-blue); }
        .auth-social-icon { width: 30px; height: 30px; border-radius: 999px; background: #1a2d45; border: 1px solid #2a4060; display: inline-flex; align-items: center; justify-content: center; }
        .auth-social-btn img { width: 20px; height: 20px; object-fit: contain; }

        .auth-switch { margin: 0.95rem 0 0; text-align: center; color: #8a9ab8; font-size: 0.92rem; }
        .auth-switch a { color: var(--brand-blue); font-weight: 700; }

        .auth-art { position: absolute; right: 1.1rem; bottom: 1.5rem; width: 220px; }
        .auth-art-curve { position: absolute; right: 58px; top: -150px; width: 150px; height: 150px; border-right: 2px dashed var(--brand-red); border-bottom: 2px dashed var(--brand-red); border-radius: 0 0 150px 0; }
        .auth-art-curve::after { content: ''; position: absolute; right: -8px; bottom: -8px; border-top: 7px solid transparent; border-bottom: 7px solid transparent; border-left: 12px solid var(--brand-red); transform: rotate(18deg); }
        .auth-art-dot { position: absolute; width: 16px; height: 16px; border-radius: 50%; }
        .auth-art-dot-top { right: 190px; top: -158px; background: var(--brand-blue); box-shadow: 0 0 0 6px rgba(26, 115, 232, 0.18); }
        .auth-art-dot-bottom { right: 16px; top: -22px; background: var(--brand-red); box-shadow: 0 0 0 6px rgba(232, 36, 26, 0.18); }
        .auth-art-alert { background: #111d2e; border: 1px solid #1e3050; border-radius: 14px; box-shadow: 0 10px 24px rgba(0,0,0,0.4); padding: 0.7rem 0.75rem 0.6rem; }
        .auth-art-alert-input { border: 1px solid rgba(232,36,26,0.3); border-radius: 8px; min-height: 36px; display: flex; align-items: center; padding: 0 0.6rem; color: #8a9ab8; font-size: 0.85rem; letter-spacing: 0.04em; font-family: ui-monospace, monospace; }
        .auth-art-alert p { margin: 0.45rem 0 0; color: var(--brand-red); font-size: 0.72rem; line-height: 1.25; }

        @media (max-width: 1100px) { .auth-art { display: none; } }
        @media (max-width: 860px) {
            .auth-shell { grid-template-columns: 1fr; width: 100%; }
            .auth-visual { padding: 3.1rem 1.25rem 1.6rem; align-items: center; text-align: center; }
            .auth-logo-name, .auth-title, .auth-tagline { width: auto; }
            .auth-title { font-size: 2.35rem; }
            .auth-card { padding: 1.25rem; }
            .auth-card-panel { width: 100%; margin-left: 0; }
            .auth-pw-row, .auth-social { grid-template-columns: 1fr; }
        }
    </style>

    <script>
        (function () {
            const toggleButtons = document.querySelectorAll('.auth-eye-btn');
            toggleButtons.forEach((btn) => {
                btn.addEventListener('click', () => {
                    const targetId = btn.getAttribute('data-target');
                    const input = targetId ? document.getElementById(targetId) : null;
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