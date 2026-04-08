@php
    $myTicketsUrl = route('tickets.index');
@endphp

<nav class="site-nav" aria-label="Main navigation">
    <a href="{{ route('home') }}" class="brand" aria-label="Wado Tickets home">
        <img src="{{ asset('images/wado-logo.png') }}" alt="Wado Tickets">
    </a>

    <a href="{{ $myTicketsUrl }}" class="mobile-ticket-link" aria-label="My tickets">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 8a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v3a2 2 0 0 0 0 4v3a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-3a2 2 0 0 0 0-4V8zm5 0v10m5-10v10" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
    </a>

    <button class="mobile-menu-toggle" type="button" aria-expanded="false" aria-controls="site-nav-panel">
        <span></span>
        <span></span>
        <span></span>
    </button>

    <div class="nav-panel" id="site-nav-panel">
        <div class="nav-links">
            <a href="{{ route('home') }}">Home</a>
            <a href="{{ route('events.index') }}">Events</a>
            @auth
                <a href="{{ $myTicketsUrl }}">My Tickets</a>
                @if (auth()->user()->isGateStaff())
                    <a href="{{ route('gate.portal') }}">Gate Portal</a>
                @endif
                @if (auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
                    <a href="{{ url('/dashboard') }}">Admin</a>
                    @if (auth()->user()->isSuperAdmin())
                        <a href="{{ \App\Filament\Resources\PaymentTransactions\PaymentTransactionResource::getUrl() }}">Payments</a>
                    @endif
                @endif
            @endauth
        </div>

        <div class="nav-actions">
            @guest
                <a href="{{ route('login') }}" class="btn btn-ghost">Log in</a>
                <a href="{{ route('register') }}" class="btn btn-solid">Sign up</a>
            @endguest

            @auth
                @if (auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
                    <a href="{{ url('/dashboard/events/create') }}" class="btn btn-solid">Create Event</a>
                @endif
                @if (auth()->user()->isGateStaff())
                    <a href="{{ route('tickets.verify.index') }}" class="btn btn-ghost">Scanner</a>
                @endif
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-ghost">Log out</button>
                </form>
            @endauth
        </div>
    </div>
</nav>

<style>
    .site-nav {
        width: min(var(--site-width), calc(100% - 2rem));
        margin: 0 auto;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 0.8rem 1rem;
        border-radius: 999px;
        border: 1px solid rgba(255, 255, 255, 0.18);
        background: rgba(4, 6, 10, 0.44);
        backdrop-filter: blur(12px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
    }

    .brand {
        display: inline-flex;
        align-items: center;
        text-decoration: none;
        color: #ffffff;
        font-weight: 700;
        letter-spacing: 0.01em;
        white-space: nowrap;
    }

    .brand img {
        height: 34px;
        width: auto;
        display: block;
    }

    .nav-panel,
    .nav-links,
    .nav-actions {
        display: flex;
        align-items: center;
        gap: 0.6rem;
    }

    .nav-panel {
        flex: 1;
        justify-content: space-between;
    }

    .nav-links a {
        text-decoration: none;
        color: #ededed;
        font-weight: 500;
        font-size: 0.95rem;
        padding: 0.35rem 0.75rem;
        border-radius: 999px;
        transition: background 0.2s ease;
    }

    .nav-links a:hover {
        background: rgba(255, 255, 255, 0.12);
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        text-decoration: none;
        border: 1px solid transparent;
        padding: 0.45rem 0.9rem;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        transition: transform 0.2s ease, filter 0.2s ease;
    }

    .btn:hover {
        transform: translateY(-1px);
        filter: brightness(1.03);
    }

    .btn-solid {
        color: #ffffff;
        background: linear-gradient(90deg, #ef4444, #b91c1c);
        box-shadow: 0 8px 20px rgba(185, 28, 28, 0.4);
    }

    .btn-ghost {
        color: #ffffff;
        background: rgba(255, 255, 255, 0.08);
        border-color: rgba(255, 255, 255, 0.14);
    }

    .nav-actions form {
        margin: 0;
    }

    .mobile-menu-toggle,
    .mobile-ticket-link {
        display: none;
    }

    .mobile-menu-toggle {
        width: 2.9rem;
        height: 2.9rem;
        border-radius: 999px;
        border: 1px solid rgba(255,255,255,0.14);
        background: rgba(255,255,255,0.08);
        color: #fff;
        align-items: center;
        justify-content: center;
        gap: 0.25rem;
        flex-direction: column;
        cursor: pointer;
    }

    .mobile-menu-toggle span {
        width: 1.15rem;
        height: 2px;
        border-radius: 999px;
        background: currentColor;
    }

    .mobile-ticket-link {
        width: 2.9rem;
        height: 2.9rem;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        color: #fff;
        border: 1px solid rgba(255,255,255,0.14);
        background: rgba(255,255,255,0.08);
    }

    .mobile-ticket-link svg {
        width: 1.25rem;
        height: 1.25rem;
    }

    @media (max-width: 860px) {
        .site-nav {
            border-radius: 24px;
            display: grid;
            grid-template-columns: 1fr auto auto;
            align-items: center;
        }

        .brand {
            width: auto;
            text-align: left;
        }

        .mobile-menu-toggle,
        .mobile-ticket-link {
            display: inline-flex;
        }

        .nav-panel {
            display: none;
            grid-column: 1 / -1;
            width: 100%;
            flex-direction: column;
            align-items: stretch;
            padding-top: 0.7rem;
        }

        .site-nav.is-open .nav-panel {
            display: flex;
        }

        .nav-links,
        .nav-actions {
            width: 100%;
            flex-direction: column;
            align-items: stretch;
        }

        .nav-actions form,
        .nav-actions form button,
        .nav-actions .btn {
            width: 100%;
        }

        .nav-actions form button {
            justify-content: center;
        }
    }

    @media (max-width: 560px) {
        .site-nav {
            width: calc(100% - 1rem);
            padding: 0.75rem;
        }
    }
</style>

<script>
    (() => {
        const nav = document.querySelector('.site-nav');
        const toggle = nav?.querySelector('.mobile-menu-toggle');

        if (!nav || !toggle) return;

        toggle.addEventListener('click', () => {
            const isOpen = nav.classList.toggle('is-open');
            toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });
    })();
</script>
