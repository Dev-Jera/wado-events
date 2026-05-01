@php
    $myTicketsUrl = route('tickets.index');
@endphp

<nav class="site-nav" aria-label="Main navigation">
    <a href="{{ route('home') }}" class="brand" aria-label="Wado Tickets home">
        <img src="{{ asset('images/logos/Wado Ticketing.png') }}" alt="Wado Tickets">
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
            <a href="{{ route('home') }}" @class(['active' => request()->routeIs('home')])>Home</a>
            <a href="{{ route('events.index') }}" @class(['active' => request()->routeIs('events.*')])>Events</a>
            <a href="{{ route('ticket-packages.index') }}" @class(['active' => request()->routeIs('ticket-packages.*')])>Packages</a>
            <a href="{{ route('contact') }}" @class(['active' => request()->routeIs('contact*')])>Contact</a>
            @auth
                <a href="{{ $myTicketsUrl }}" @class(['active' => request()->routeIs('tickets.index')])>My Tickets</a>
            @endauth
        </div>

        <div class="nav-actions">
            @guest
                <a href="{{ route('login') }}" class="btn btn-ghost">Log in</a>
                <a href="{{ route('register') }}" class="btn btn-solid">Sign up</a>
            @endguest

            @auth
                <div class="nav-user-menu" id="nav-user-menu">
                    <button type="button" class="nav-user-btn" id="nav-user-toggle" aria-haspopup="true" aria-expanded="false">
                        <span class="nav-user-avatar">{{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
                        <span class="nav-user-name">{{ explode(' ', auth()->user()->name)[0] }}</span>
                        <svg class="nav-user-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
                    </button>
                    <div class="nav-user-dropdown" id="nav-user-dropdown" role="menu">
                        <a href="{{ route('profile.show') }}" class="nav-dd-item" role="menuitem" @class(['nav-dd-item-active' => request()->routeIs('profile.*')])>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.582-7 8-7s8 3 8 7"/></svg>
                            Profile
                        </a>
                        <div class="nav-dd-divider"></div>
                        <form method="POST" action="{{ route('logout') }}" class="nav-dd-form">
                            @csrf
                            <button type="submit" class="nav-dd-item nav-dd-logout" role="menuitem">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/></svg>
                                Log out
                            </button>
                        </form>
                    </div>
                </div>
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
        height: 44px;
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
        transition: background 0.2s ease, color 0.2s ease;
    }

    .nav-links a:hover {
        background: rgba(255, 255, 255, 0.12);
        color: #ffffff;
    }

    /* Active page link — white bg, dark text so it's always readable */
    .nav-links a.active {
        background: #ffffff;
        color: #0d1b3e;
        font-weight: 700;
    }

    .nav-links a.active:hover {
        background: #f1f5f9;
        color: #0d1b3e;
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

    /* ── User menu ── */
    .nav-user-menu { position: relative; }

    .nav-user-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        height: 36px;
        padding: 0 0.65rem 0 0.45rem;
        border-radius: 999px;
        border: 1px solid rgba(255,255,255,0.14);
        background: rgba(255,255,255,0.08);
        color: #fff;
        font-size: 0.88rem;
        font-weight: 600;
        font-family: inherit;
        cursor: pointer;
        transition: background 0.15s;
    }

    .nav-user-btn:hover { background: rgba(255,255,255,0.14); }

    .nav-user-avatar {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: #1a73e8;
        display: grid;
        place-items: center;
        font-size: 0.72rem;
        font-weight: 800;
        flex-shrink: 0;
    }

    .nav-user-name { max-width: 90px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .nav-user-chevron { width: 14px; height: 14px; color: rgba(255,255,255,0.6); transition: transform 0.2s; }
    .nav-user-menu.is-open .nav-user-chevron { transform: rotate(180deg); }

    .nav-user-dropdown {
        display: none;
        position: absolute;
        top: calc(100% + 0.5rem);
        right: 0;
        min-width: 180px;
        background: #111d2e;
        border: 1px solid #1e3050;
        border-radius: 14px;
        padding: 0.35rem;
        box-shadow: 0 16px 40px rgba(0,0,0,0.5);
        z-index: 50;
    }

    .nav-user-menu.is-open .nav-user-dropdown { display: block; }

    .nav-dd-item {
        display: flex;
        align-items: center;
        gap: 0.55rem;
        width: 100%;
        padding: 0.5rem 0.7rem;
        border-radius: 9px;
        font-size: 0.88rem;
        font-weight: 600;
        color: #c0cfe8;
        text-decoration: none;
        background: transparent;
        border: none;
        font-family: inherit;
        cursor: pointer;
        transition: background 0.12s, color 0.12s;
    }

    .nav-dd-item svg { width: 15px; height: 15px; flex-shrink: 0; }
    .nav-dd-item:hover { background: rgba(255,255,255,0.07); color: #fff; }
    .nav-dd-item-active { color: #fff; background: rgba(26,115,232,0.15); }

    .nav-dd-divider { height: 1px; background: #1e3050; margin: 0.3rem 0.4rem; }
    .nav-dd-form { margin: 0; }
    .nav-dd-logout { color: #e8241a; }
    .nav-dd-logout:hover { background: rgba(232,36,26,0.1); color: #e8241a; }

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

        .nav-user-menu { width: 100%; }

        .nav-user-btn {
            width: 100%;
            border-radius: 10px;
            justify-content: flex-start;
            height: 44px;
            padding: 0 0.85rem;
        }

        .nav-user-name { max-width: none; flex: 1; text-align: left; }

        .nav-user-dropdown {
            position: static;
            box-shadow: none;
            border: none;
            background: rgba(255,255,255,0.04);
            border-radius: 10px;
            margin-top: 0.4rem;
        }

        /* On mobile, active link uses a subtle left border instead of white pill */
        .nav-links a.active {
            background: rgba(255, 255, 255, 0.12);
            color: #ffffff;
            border-left: 3px solid #ef4444;
            border-radius: 8px;
            padding-left: calc(0.75rem - 3px);
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

        if (nav && toggle) {
            toggle.addEventListener('click', () => {
                const isOpen = nav.classList.toggle('is-open');
                toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            });
        }

        const userMenu = document.getElementById('nav-user-menu');
        const userToggle = document.getElementById('nav-user-toggle');
        if (userMenu && userToggle) {
            userToggle.addEventListener('click', (e) => {
                e.stopPropagation();
                const open = userMenu.classList.toggle('is-open');
                userToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            });
            document.addEventListener('click', (e) => {
                if (!userMenu.contains(e.target)) {
                    userMenu.classList.remove('is-open');
                    userToggle.setAttribute('aria-expanded', 'false');
                }
            });
        }
    })();
</script>