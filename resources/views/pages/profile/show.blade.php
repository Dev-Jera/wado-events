@extends('layouts.app')

@section('content')

<div class="prof-page">

    {{-- Page header --}}
    <div class="prof-header">
        <div class="prof-header-inner">
            <div class="prof-avatar">
                @if ($user->profile_image_path)
                    <img src="{{ Storage::disk('public')->url($user->profile_image_path) }}" alt="{{ $user->name }}">
                @else
                    <span>{{ strtoupper(mb_substr($user->name, 0, 1)) }}</span>
                @endif
            </div>
            <div>
                <h1>{{ $user->name }}</h1>
                <p class="prof-email-sub">{{ $user->email }}</p>
            </div>
        </div>
    </div>

    <div class="prof-body">

        {{-- ── Info form ── --}}
        <section class="prof-card" id="info">
            <h2>Personal information</h2>
            <p class="prof-section-sub">Update your name, email address, and phone number.</p>

            <form method="POST" action="{{ route('profile.update') }}" class="prof-form">
                @csrf
                @method('PUT')

                <div class="prof-field">
                    <label for="name">Full name</label>
                    <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required>
                    @error('name') <small class="prof-err">{{ $message }}</small> @enderror
                </div>

                <div class="prof-field">
                    <label for="email">Email address</label>
                    <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required>
                    @error('email') <small class="prof-err">{{ $message }}</small> @enderror
                </div>

                <div class="prof-field">
                    <label for="phone">Phone number <span class="prof-optional">(optional)</span></label>
                    <input id="phone" type="tel" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="+256 700 000 000">
                    @error('phone') <small class="prof-err">{{ $message }}</small> @enderror
                </div>

                <div class="prof-actions">
                    <button type="submit" class="prof-btn prof-btn-primary">Save changes</button>
                </div>
            </form>
        </section>

        {{-- ── Password form ── --}}
        @if (!$user->provider)
        <section class="prof-card" id="password">
            <h2>Change password</h2>
            <p class="prof-section-sub">Choose a strong password with at least 8 characters, including upper & lowercase letters and a number.</p>

            <form method="POST" action="{{ route('profile.password') }}" class="prof-form">
                @csrf
                @method('PUT')

                <div class="prof-field">
                    <label for="current_password">Current password</label>
                    <div class="prof-pw-wrap">
                        <input id="current_password" type="password" name="current_password" required autocomplete="current-password">
                        <button type="button" class="prof-eye" data-target="current_password" aria-label="Show password">
                            <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M2 12s3.6-6 10-6 10 6 10 6-3.6 6-10 6S2 12 2 12z" fill="none" stroke="currentColor" stroke-width="1.8"/><circle cx="12" cy="12" r="2.8" fill="none" stroke="currentColor" stroke-width="1.8"/><path class="eye-off" d="M4 20 20 4" fill="none" stroke="currentColor" stroke-width="1.8"/></svg>
                        </button>
                    </div>
                    @error('current_password') <small class="prof-err">{{ $message }}</small> @enderror
                </div>

                <div class="prof-field">
                    <label for="password">New password</label>
                    <div class="prof-pw-wrap">
                        <input id="password" type="password" name="password" required autocomplete="new-password">
                        <button type="button" class="prof-eye" data-target="password" aria-label="Show password">
                            <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M2 12s3.6-6 10-6 10 6 10 6-3.6 6-10 6S2 12 2 12z" fill="none" stroke="currentColor" stroke-width="1.8"/><circle cx="12" cy="12" r="2.8" fill="none" stroke="currentColor" stroke-width="1.8"/><path class="eye-off" d="M4 20 20 4" fill="none" stroke="currentColor" stroke-width="1.8"/></svg>
                        </button>
                    </div>
                    @error('password') <small class="prof-err">{{ $message }}</small> @enderror
                </div>

                <div class="prof-field">
                    <label for="password_confirmation">Confirm new password</label>
                    <div class="prof-pw-wrap">
                        <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password">
                        <button type="button" class="prof-eye" data-target="password_confirmation" aria-label="Show password">
                            <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M2 12s3.6-6 10-6 10 6 10 6-3.6 6-10 6S2 12 2 12z" fill="none" stroke="currentColor" stroke-width="1.8"/><circle cx="12" cy="12" r="2.8" fill="none" stroke="currentColor" stroke-width="1.8"/><path class="eye-off" d="M4 20 20 4" fill="none" stroke="currentColor" stroke-width="1.8"/></svg>
                        </button>
                    </div>
                </div>

                <div class="prof-actions">
                    <button type="submit" class="prof-btn prof-btn-primary">Update password</button>
                </div>
            </form>
        </section>
        @else
        <section class="prof-card prof-card-muted" id="password">
            <h2>Password</h2>
            <p class="prof-section-sub">You signed in with {{ ucfirst($user->provider) }}. Password-based login is not available for social accounts.</p>
        </section>
        @endif

        {{-- ── Quick links ── --}}
        <section class="prof-card prof-card-links">
            <a href="{{ route('tickets.index') }}" class="prof-link-row">
                <span class="prof-link-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 8a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v3a2 2 0 0 0 0 4v3a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-3a2 2 0 0 0 0-4V8zm5 0v10m5-10v10"/></svg>
                </span>
                <span>
                    <strong>My Tickets</strong>
                    <em>View all your event tickets</em>
                </span>
                <svg class="prof-link-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
            </a>
            <a href="{{ route('events.index') }}" class="prof-link-row">
                <span class="prof-link-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                </span>
                <span>
                    <strong>Browse Events</strong>
                    <em>Find your next experience</em>
                </span>
                <svg class="prof-link-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
            </a>
            <div class="prof-link-row prof-link-row-danger">
                <span class="prof-link-icon prof-link-icon-danger">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/></svg>
                </span>
                <span>
                    <strong>Sign out</strong>
                    <em>Log out of your account</em>
                </span>
                <form method="POST" action="{{ route('logout') }}" style="margin-left:auto">
                    @csrf
                    <button type="submit" class="prof-logout-btn">Log out</button>
                </form>
            </div>
        </section>

    </div>
</div>

<style>
    :root {
        --prof-blue: #c0283c;
        --prof-red: #e8241a;
        --prof-bg: #07101c;
        --prof-surface: #111d2e;
        --prof-border: #1e3050;
        --prof-muted: #8a9ab8;
        --prof-label: #c0cfe8;
    }

    .prof-page {
        min-height: 100vh;
        background: var(--prof-bg);
        padding-top: 5.5rem;
        padding-bottom: 3rem;
    }

    /* ── Header ── */
    .prof-header {
        background: linear-gradient(135deg, #0a1525 0%, #0d1e38 100%);
        border-bottom: 1px solid var(--prof-border);
        padding: 2rem 1rem 1.75rem;
    }

    .prof-header-inner {
        width: min(700px, 100%);
        margin: 0 auto;
        display: flex;
        align-items: center;
        gap: 1.25rem;
    }

    .prof-avatar {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        background: var(--prof-blue);
        border: 2px solid rgba(192,40,60,0.4);
        display: grid;
        place-items: center;
        flex-shrink: 0;
        overflow: hidden;
        font-size: 1.6rem;
        font-weight: 800;
        color: #fff;
    }

    .prof-avatar img { width: 100%; height: 100%; object-fit: cover; }

    .prof-header h1 { margin: 0; color: #fff; font-size: 1.45rem; font-weight: 800; }
    .prof-email-sub { margin: 0.2rem 0 0; color: var(--prof-muted); font-size: 0.9rem; }

    /* ── Body ── */
    .prof-body {
        width: min(700px, 100%);
        margin: 0 auto;
        padding: 1.75rem 1rem 0;
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
    }

    /* ── Cards ── */
    .prof-card {
        background: var(--prof-surface);
        border: 1px solid var(--prof-border);
        border-radius: 16px;
        padding: 1.5rem 1.4rem;
    }

    .prof-card h2 {
        margin: 0 0 0.2rem;
        color: #fff;
        font-size: 1.05rem;
        font-weight: 800;
    }

    .prof-section-sub {
        margin: 0 0 1.25rem;
        color: var(--prof-muted);
        font-size: 0.86rem;
        line-height: 1.45;
    }

    .prof-card-muted { opacity: 0.7; }

    /* ── Form ── */
    .prof-form { display: grid; gap: 1rem; }

    .prof-field { display: grid; gap: 0.35rem; }
    .prof-field label { color: var(--prof-label); font-size: 0.84rem; font-weight: 700; }
    .prof-optional { color: var(--prof-muted); font-weight: 400; }

    .prof-field input[type="text"],
    .prof-field input[type="email"],
    .prof-field input[type="tel"],
    .prof-field input[type="password"] {
        width: 100%;
        height: 42px;
        border: 1px solid var(--prof-border);
        border-radius: 8px;
        background: #0d1929;
        color: #fff;
        padding: 0 0.85rem;
        font-size: 0.9rem;
        font-family: inherit;
        transition: border-color 0.15s, box-shadow 0.15s;
    }

    .prof-field input:focus {
        outline: none;
        border-color: var(--prof-blue);
        box-shadow: 0 0 0 3px rgba(192,40,60,0.12);
    }

    .prof-err { color: var(--prof-red); font-size: 0.75rem; font-weight: 600; }

    .prof-pw-wrap { position: relative; }
    .prof-pw-wrap input { padding-right: 2.5rem; }

    .prof-eye {
        position: absolute;
        right: 0.55rem;
        top: 50%;
        transform: translateY(-50%);
        width: 28px;
        height: 28px;
        border: 0;
        background: transparent;
        color: var(--prof-blue);
        cursor: pointer;
        display: grid;
        place-items: center;
        border-radius: 6px;
    }

    .prof-eye svg { width: 18px; height: 18px; }
    .prof-eye .eye-off { opacity: 1; }
    .prof-eye.is-visible .eye-off { opacity: 0; }

    .prof-actions { padding-top: 0.25rem; }

    .prof-btn {
        height: 42px;
        padding: 0 1.4rem;
        border: none;
        border-radius: 8px;
        font-size: 0.9rem;
        font-weight: 700;
        cursor: pointer;
        font-family: inherit;
        transition: filter 0.15s;
    }

    .prof-btn:hover { filter: brightness(1.08); }

    .prof-btn-primary { background: var(--prof-blue); color: #fff; }

    /* ── Quick links card ── */
    .prof-card-links { padding: 0; overflow: hidden; }

    .prof-link-row {
        display: flex;
        align-items: center;
        gap: 0.9rem;
        padding: 1rem 1.2rem;
        text-decoration: none;
        color: inherit;
        border-bottom: 1px solid var(--prof-border);
        transition: background 0.15s;
    }

    .prof-link-row:last-child { border-bottom: none; }
    .prof-link-row:hover { background: rgba(255,255,255,0.04); }

    .prof-link-row strong { display: block; color: #fff; font-size: 0.9rem; font-weight: 700; }
    .prof-link-row em { display: block; color: var(--prof-muted); font-size: 0.8rem; font-style: normal; }

    .prof-link-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: rgba(192,40,60,0.15);
        border: 1px solid rgba(192,40,60,0.2);
        display: grid;
        place-items: center;
        flex-shrink: 0;
        color: var(--prof-blue);
    }

    .prof-link-icon svg { width: 18px; height: 18px; }

    .prof-link-icon-danger {
        background: rgba(232,36,26,0.12);
        border-color: rgba(232,36,26,0.2);
        color: var(--prof-red);
    }

    .prof-link-chevron { width: 16px; height: 16px; color: var(--prof-muted); margin-left: auto; }

    .prof-link-row-danger { cursor: default; }
    .prof-link-row-danger:hover { background: transparent; }

    .prof-logout-btn {
        height: 34px;
        padding: 0 1rem;
        border: 1px solid rgba(232,36,26,0.35);
        border-radius: 8px;
        background: rgba(232,36,26,0.1);
        color: var(--prof-red);
        font-size: 0.82rem;
        font-weight: 700;
        cursor: pointer;
        font-family: inherit;
        transition: background 0.15s;
    }

    .prof-logout-btn:hover { background: rgba(232,36,26,0.2); }

    @media (max-width: 600px) {
        .prof-page { padding-top: 5rem; }
        .prof-header { padding: 1.5rem 1rem 1.25rem; }
        .prof-card { padding: 1.2rem 1rem; }
    }
</style>

<script>
(function () {
    document.querySelectorAll('.prof-eye').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var id = btn.getAttribute('data-target');
            var input = document.getElementById(id);
            if (!input) return;
            var show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            btn.classList.toggle('is-visible', show);
            btn.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
        });
    });
})();
</script>

@endsection
