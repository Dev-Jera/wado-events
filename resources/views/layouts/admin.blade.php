<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') | Wado Tickets</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background: #f3f6fb;
            color: #101828;
        }
        .admin-shell { display: grid; grid-template-columns: 280px 1fr; min-height: 100vh; }
        .admin-sidebar { background: linear-gradient(180deg, #08111f 0%, #0f1b31 100%); color: #f8fafc; padding: 2rem 1.25rem; }
        .admin-brand { display: inline-block; color: #fff; text-decoration: none; font-weight: 800; font-size: 1.1rem; margin-bottom: 1.8rem; }
        .admin-nav { display: grid; gap: 0.55rem; }
        .admin-nav a { color: #d7e1f2; text-decoration: none; padding: 0.85rem 1rem; border-radius: 16px; background: rgba(255, 255, 255, 0.05); }
        .admin-content { padding: 2rem; }
        .admin-topbar { display: flex; justify-content: space-between; align-items: center; gap: 1rem; margin-bottom: 1.5rem; }
        .admin-topbar h1 { margin: 0; font-size: clamp(1.8rem, 3vw, 2.8rem); }
        .back-home { color: #b45309; text-decoration: none; font-weight: 700; }
        @media (max-width: 900px) { .admin-shell { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <div class="admin-shell">
        <aside class="admin-sidebar">
            <a href="{{ route('admin.events.index') }}" class="admin-brand">Wado Admin</a>
            <nav class="admin-nav" aria-label="Admin navigation">
                <a href="{{ route('admin.events.index') }}">Dashboard</a>
                <a href="{{ route('admin.events.create') }}">Create event</a>
                <a href="{{ route('admin.categories.index') }}">Categories</a>
                <a href="{{ route('events.index') }}">Public events</a>
            </nav>
        </aside>
        <main class="admin-content">
            <div class="admin-topbar">
                <div>
                    <p style="margin:0;color:#b45309;font-weight:700;text-transform:uppercase;letter-spacing:.12em;font-size:.8rem;">Admin dashboard</p>
                    <h1>@yield('heading', 'Events')</h1>
                </div>
                <a href="{{ route('home') }}" class="back-home">Back to website</a>
            </div>
            @yield('content')
        </main>
    </div>
</body>
</html>
