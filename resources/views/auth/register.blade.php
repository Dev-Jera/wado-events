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
                <h2 class="auth-title"><span>Sign Up</span><br>registration<br>form</h2>
                <p class="auth-tagline">Password entry step and error output</p>

               
                
            </aside>

            <div class="auth-card">
                <div class="auth-card-panel">
                <h1>Sign Up</h1>
                <p class="auth-copy">Let's get started. Join once, then buy tickets and access My Tickets anytime.</p>

                <form method="POST" action="{{ route('register.store') }}" class="auth-form">
                    @csrf

                    <label>
                        <span>Full name</span>
                        <input type="text" name="name" value="{{ old('name') }}" required autofocus>
                        @error('name') <small>{{ $message }}</small> @enderror
                    </label>

                    <label>
                        <span>Email address</span>
                        <input type="email" name="email" value="{{ old('email') }}" required>
                        @error('email') <small>{{ $message }}</small> @enderror
                    </label>

                    <div class="auth-field-hint">We'll send your tickets here</div>

                    <div class="auth-pw-row">
                        <label>
                            <span>Password</span>
                            <div class="auth-password-wrap">
                                <input id="register-password" class="js-password-input" type="password" name="password" required>
                                <button type="button" class="auth-eye-btn" data-target="register-password" aria-label="Show password">
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
                            <span>Confirm password</span>
                            <div class="auth-password-wrap">
                                <input id="register-password-confirm" type="password" name="password_confirmation" required>
                                <button type="button" class="auth-eye-btn" data-target="register-password-confirm" aria-label="Show password confirmation">
                                    <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                                        <path d="M2 12s3.6-6 10-6 10 6 10 6-3.6 6-10 6S2 12 2 12z" fill="none" stroke="currentColor" stroke-width="1.8"/>
                                        <circle cx="12" cy="12" r="2.8" fill="none" stroke="currentColor" stroke-width="1.8"/>
                                        <path class="eye-off" d="M4 20 20 4" fill="none" stroke="currentColor" stroke-width="1.8"/>
                                    </svg>
                                </button>
                            </div>
                        </label>
                    </div>

                    <button type="submit" class="auth-btn">Create account</button>
                </form>

                <div class="auth-divider"><hr><span>or sign up with</span><hr></div>

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

                <p class="auth-switch">Already have an account? <a href="{{ route('login') }}">Log in</a></p>
                </div>

                <div class="auth-art" aria-hidden="true">
                    <div class="auth-art-curve"></div>
                    <span class="auth-art-dot auth-art-dot-top"></span>
                    <span class="auth-art-dot auth-art-dot-bottom"></span>
                    <div class="auth-art-alert">
                        <div class="auth-art-alert-input">
                            <span id="auth-art-password-preview">************</span>
                            <span class="auth-art-mini-eye" aria-hidden="true">
                                <svg viewBox="0 0 24 24" focusable="false">
                                    <path d="M2 12s3.6-6 10-6 10 6 10 6-3.6 6-10 6S2 12 2 12z" fill="none" stroke="currentColor" stroke-width="1.8"/>
                                    <circle cx="12" cy="12" r="2.8" fill="none" stroke="currentColor" stroke-width="1.8"/>
                                    <path d="M4 20 20 4" fill="none" stroke="currentColor" stroke-width="1.8"/>
                                </svg>
                            </span>
                        </div>
                        <p id="auth-art-password-feedback"><span class="auth-art-warn-icon">!</span><span id="auth-art-password-feedback-text">Password is too simple or there are not enough characters in it</span></p>
                    </div>
                    
                </div>
            </div>
        </div>
    </section>

    <style>
        .auth-page { min-height: 100vh; background: #edf2f9; }
        .auth-shell { min-height: 100vh; width: min(1120px, 100%); margin: 0 auto; display: grid; grid-template-columns: 0.96fr 1.04fr; }
        .auth-visual { background: #eaf1fb; padding: clamp(6rem, 15vh, 8.4rem) 0.9rem 2rem 1rem; display: flex; flex-direction: column; align-items: flex-end; justify-content: flex-start; text-align: left; }
        .auth-logo-mark { width: 58px; height: 58px; margin-top: 0; border-radius: 14px; background: #1582d2; display: grid; place-items: center; overflow: hidden; box-shadow: 0 10px 24px rgba(21, 130, 210, 0.2); }
        .auth-logo-mark img { width: 38px; height: 38px; object-fit: contain; }
        .auth-logo-name { margin-top: 0.85rem; color: #1178ca; font-size: 1.05rem; font-weight: 900; text-decoration: none; width: min(360px, 100%); }
        .auth-title { margin: 1.4rem 0 0; color: #060a11; font-size: clamp(2.2rem, 4.2vw, 3.5rem); line-height: 1.02; font-weight: 800; width: min(360px, 100%); }
        .auth-title span { color: #0f5dc6; }
        .auth-tagline { margin: 1rem 0 0; color: #6d7585; font-size: 1rem; width: min(360px, 100%); max-width: none; }
        .auth-event-wrap { width: 100%; display: flex; justify-content: flex-start; margin: 2rem 0 1.2rem; }
        .auth-event-image { width: 150px; height: auto; object-fit: contain; }
        .auth-highlight { width: min(220px, 100%); border-radius: 0 0 22px 22px; background: linear-gradient(180deg, #f2f5f9 0%, #6f7f94 100%); color: #fff; padding: 1rem 1.25rem 1.1rem; text-align: left; }
        .auth-highlight span { display: block; margin-bottom: 0.55rem; color: rgba(255,255,255,0.82); font-size: 0.72rem; font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase; }
        .auth-highlight strong { display: block; font-size: 0.96rem; line-height: 1.35; font-weight: 800; }
        .auth-trust { margin-top: 2rem; display: flex; justify-content: center; gap: 1.2rem; flex-wrap: wrap; }
        .auth-trust-item { display: flex; align-items: center; gap: 6px; max-width: 84px; color: #7d95b1; font-size: 0.78rem; text-align: left; }
        .auth-trust-dot { width: 6px; height: 6px; border-radius: 50%; background: #2ecc71; flex-shrink: 0; }
        .auth-card { background: #eaf1fb; padding: 2rem 0.6rem 2rem 0; display: flex; align-items: flex-start; justify-content: flex-start; position: relative; }
        .auth-card-panel { width: min(405px, 100%); margin-left: -10px; background: #fff; border: 1px solid #e5ebf5; border-radius: 20px; padding: 1.5rem 1.35rem 1.2rem; box-shadow: 0 12px 30px rgba(37, 74, 136, 0.1); }
        .auth-card h1 { margin: 0; color: #232645; font-size: clamp(1.75rem, 2.2vw, 2rem); line-height: 1.08; font-weight: 800; text-align: center; }
        .auth-copy { margin: 0.6rem 0 1.1rem; color: #5d6475; font-size: 0.9rem; line-height: 1.42; text-align: center; }
        .auth-form { display: grid; gap: 0.85rem; }
        .auth-form label { display: grid; gap: 0.4rem; color: #3f4658; font-size: 0.86rem; font-weight: 700; }
        .auth-form input { width: 100%; height: 42px; border: 1px solid #d7e0ef; border-radius: 8px; background: #fff; color: #23324a; padding: 0 0.8rem; font-size: 0.9rem; }
        .auth-form input:focus { outline: none; border-color: #2b78dd; box-shadow: 0 0 0 2px rgba(43, 120, 221, 0.12); }
        .auth-form small { color: #d8394f; font-size: 0.75rem; font-weight: 600; }
        .auth-field-hint { margin-top: -0.45rem; color: #637086; font-size: 0.82rem; }
        .auth-pw-row { display: grid; grid-template-columns: 1fr 1fr; gap: 0.8rem; }
        .auth-password-wrap { position: relative; }
        .auth-password-wrap input { padding-right: 2.5rem; }
        .auth-eye-btn { position: absolute; right: 0.55rem; top: 50%; transform: translateY(-50%); width: 28px; height: 28px; border: 0; border-radius: 6px; background: transparent; color: #4d607d; cursor: pointer; display: grid; place-items: center; }
        .auth-eye-btn svg { width: 18px; height: 18px; }
        .auth-eye-btn .eye-off { opacity: 1; }
        .auth-eye-btn.is-visible .eye-off { opacity: 0; }
        .auth-btn { height: 44px; border: 1px solid #0d5cc2; border-radius: 8px; background: #1662d3; color: #fff; font-size: 0.92rem; font-weight: 700; cursor: pointer; }
        .auth-divider { display: flex; align-items: center; gap: 0.8rem; margin: 1rem 0; }
        .auth-divider hr { flex: 1; border: none; border-top: 1px solid #d8e1f0; }
        .auth-divider span { color: #79869b; font-size: 0.86rem; }
        .auth-social { display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; }
        .auth-social-btn { height: 42px; border-radius: 8px; border: 1px solid #d7dfec; background: #fff; color: #263248; font-size: 0.88rem; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.5rem; }
        .auth-social-icon { width: 30px; height: 30px; border-radius: 999px; background: #fff; border: 1px solid #e0e7f2; display: inline-flex; align-items: center; justify-content: center; }
        .auth-social-btn img { width: 20px; height: 20px; object-fit: contain; }
        .auth-switch { margin: 0.95rem 0 0; text-align: center; color: #616a7d; font-size: 0.92rem; }
        .auth-switch a { color: #1662d3; font-weight: 700; }
        .auth-art { position: absolute; right: 1.1rem; bottom: 1.5rem; width: 220px; }
        .auth-art-curve { position: absolute; right: 58px; top: -150px; width: 150px; height: 150px; border-right: 2px dashed #cf4ddb; border-bottom: 2px dashed #cf4ddb; border-radius: 0 0 150px 0; }
        .auth-art-curve::after { content: ''; position: absolute; right: -8px; bottom: -8px; border-top: 7px solid transparent; border-bottom: 7px solid transparent; border-left: 12px solid #cf4ddb; transform: rotate(18deg); }
        .auth-art-dot { position: absolute; width: 16px; height: 16px; border-radius: 50%; }
        .auth-art-dot-top { right: 190px; top: -158px; background: #17bf86; box-shadow: 0 0 0 6px rgba(23, 191, 134, 0.2); }
        .auth-art-dot-bottom { right: 16px; top: -22px; background: #cc3be9; box-shadow: 0 0 0 6px rgba(204, 59, 233, 0.2); }
        .auth-art-alert { background: #fff; border: 1px solid #e8edf6; border-radius: 14px; box-shadow: 0 10px 24px rgba(37, 74, 136, 0.1); padding: 0.7rem 0.75rem 0.6rem; }
        .auth-art-alert-input { border: 1px solid #f1a5af; border-radius: 8px; min-height: 36px; display: flex; align-items: center; justify-content: space-between; padding: 0 0.6rem; color: #4b5267; font-size: 0.85rem; letter-spacing: 0.04em; font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace; }
        .auth-art-mini-eye { color: #1673d5; display: inline-flex; align-items: center; justify-content: center; }
        .auth-art-mini-eye svg { width: 14px; height: 14px; }
        .auth-art-alert p { margin: 0.45rem 0 0; color: #d74e63; font-size: 0.72rem; line-height: 1.25; display: flex; align-items: center; gap: 0.35rem; }
        .auth-art-warn-icon { width: 11px; height: 11px; border: 1px solid currentColor; border-radius: 50%; font-size: 8px; line-height: 9px; text-align: center; flex-shrink: 0; }
        .auth-art-label { position: absolute; left: -78px; bottom: -18px; background: #f020bf; color: #fff; font-size: 0.66rem; line-height: 1; font-weight: 700; padding: 5px 11px; border-radius: 4px; }
        .auth-art-label::after { content: ''; position: absolute; right: -6px; top: 50%; transform: translateY(-50%); border-top: 5px solid transparent; border-bottom: 5px solid transparent; border-left: 6px solid #f020bf; }
        @media (max-width: 1100px) { .auth-art { display: none; } }
        @media (max-width: 860px) { .auth-shell { grid-template-columns: 1fr; width: 100%; } .auth-visual { padding: 3.1rem 1.25rem 1.6rem; align-items: center; text-align: center; } .auth-logo-name, .auth-title, .auth-tagline { width: auto; } .auth-title { font-size: 2.35rem; } .auth-tagline { max-width: none; } .auth-card { padding: 1.25rem; } .auth-card-panel { width: 100%; margin-left: 0; } .auth-pw-row, .auth-social { grid-template-columns: 1fr; } .auth-event-wrap { justify-content: center; } }
    </style>

    <script>
        (function () {
            let initialized = false;

            const initRegisterPasswordUi = () => {
                if (initialized) return;

                const passwordInput = document.getElementById('register-password');
                const previewText = document.getElementById('auth-art-password-preview');
                const feedbackText = document.getElementById('auth-art-password-feedback');
                const feedbackTextValue = document.getElementById('auth-art-password-feedback-text');

                if (!passwordInput) return;
                initialized = true;

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

                const evaluateStrength = (value) => {
                    let score = 0;
                    if (value.length >= 8) score += 1;
                    if (/[A-Z]/.test(value)) score += 1;
                    if (/[a-z]/.test(value)) score += 1;
                    if (/\d/.test(value)) score += 1;

                    if (score <= 1) return { width: 25, color: '#e15567', text: 'Strength: weak', detail: 'Password is too simple or there are not enough characters in it' };
                    if (score === 2) return { width: 50, color: '#e39a35', text: 'Strength: fair', detail: 'Password is fair. Add uppercase, lowercase and numbers.' };
                    if (score === 3) return { width: 75, color: '#3ca66b', text: 'Strength: good', detail: 'Password strength is good.' };
                    return { width: 100, color: '#158d4d', text: 'Strength: strong', detail: 'Password strength is strong.' };
                };

                const render = () => {
                    const state = evaluateStrength(passwordInput.value || '');

                    if (previewText) {
                        const masked = '*'.repeat(Math.min((passwordInput.value || '').length, 18));
                        previewText.textContent = masked;
                    }
                    if (feedbackText && feedbackTextValue) {
                        feedbackTextValue.textContent = state.detail;
                        feedbackText.style.color = state.color;
                    }
                };

                ['input', 'keyup', 'change', 'paste', 'blur'].forEach((eventName) => {
                    passwordInput.addEventListener(eventName, render);
                });

                render();
            };

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initRegisterPasswordUi);
            } else {
                initRegisterPasswordUi();
            }

            window.addEventListener('pageshow', initRegisterPasswordUi);
        })();
    </script>
@endsection
