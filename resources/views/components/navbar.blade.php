<nav class="site-nav" aria-label="Main navigation">
    <a href="/" class="brand">Wado Tickets</a>

    <div class="nav-links">
        <a href="/">Home</a>
        <a href="/events">Events</a>
    </div>

    <div class="nav-actions">
        @auth
            <a href="/dashboard" class="btn btn-ghost">Dashboard</a>
            <form method="POST" action="/logout">
                @csrf
                <button class="btn btn-solid" type="submit">Logout</button>
            </form>
        @else
            <a href="/login" class="btn btn-ghost">Login</a>
            <a href="/register" class="btn btn-solid">Register</a>
        @endauth
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
        text-decoration: none;
        color: #ffffff;
        font-weight: 700;
        letter-spacing: 0.01em;
        white-space: nowrap;
    }

    .nav-links,
    .nav-actions {
        display: flex;
        align-items: center;
        gap: 0.6rem;
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

    .btn-ghost {
        color: #ffffff;
        border-color: rgba(255, 255, 255, 0.28);
        background: rgba(255, 255, 255, 0.05);
    }

    .btn-solid {
        color: #ffffff;
        background: linear-gradient(90deg, #ef4444, #b91c1c);
        box-shadow: 0 8px 20px rgba(185, 28, 28, 0.4);
    }

    .nav-actions form {
        margin: 0;
    }

    @media (max-width: 860px) {
        .site-nav {
            border-radius: 16px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .brand {
            width: 100%;
            text-align: center;
        }
    }

    @media (max-width: 560px) {
        .site-nav {
            width: calc(100% - 1rem);
            padding: 0.75rem;
        }

        .nav-links {
            width: 100%;
            justify-content: center;
        }

        .nav-actions {
            width: 100%;
            justify-content: center;
            flex-wrap: wrap;
        }
    }
</style>