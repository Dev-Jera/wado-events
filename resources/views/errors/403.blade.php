<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Access Denied — Wado Events</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#c0283c">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --maroon: #c0283c;
            --maroon-dark: #a01e2e;
            --bg: #150508;
            --card: rgba(255,255,255,.05);
            --border: rgba(255,255,255,.10);
            --font: 'Quicksand', 'Segoe UI', sans-serif;
        }
        body {
            font-family: var(--font);
            background: var(--bg);
            color: #f4f4f4;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            text-align: center;
        }
        .err-logo {
            display: flex;
            align-items: center;
            gap: .6rem;
            margin-bottom: 3rem;
            text-decoration: none;
            color: #fff;
        }
        .err-logo img { height: 54px; width: auto; display: block; }
        .err-code {
            font-size: clamp(5rem, 22vw, 9rem);
            font-weight: 800;
            line-height: 1;
            letter-spacing: -.04em;
            background: linear-gradient(135deg, #fff 0%, rgba(192,40,60,.7) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: .5rem;
        }
        .err-title {
            font-size: clamp(1.2rem, 4vw, 1.7rem);
            font-weight: 700;
            color: #fff;
            margin-bottom: .7rem;
        }
        .err-sub {
            font-size: clamp(.88rem, 2vw, 1rem);
            color: rgba(255,215,220,.65);
            line-height: 1.6;
            max-width: 400px;
            margin-bottom: 2.5rem;
        }
        .err-actions {
            display: flex;
            flex-wrap: wrap;
            gap: .75rem;
            justify-content: center;
        }
        .err-btn {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            padding: .7rem 1.6rem;
            border-radius: 999px;
            font-size: .92rem;
            font-weight: 700;
            font-family: var(--font);
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: background .18s, box-shadow .18s;
        }
        .err-btn-primary {
            background: var(--maroon);
            color: #fff;
            box-shadow: 0 4px 18px rgba(192,40,60,.35);
        }
        .err-btn-primary:hover {
            background: var(--maroon-dark);
            box-shadow: 0 6px 22px rgba(192,40,60,.48);
        }
        .err-btn-ghost {
            background: var(--card);
            color: rgba(255,215,220,.8);
            border: 1px solid var(--border);
        }
        .err-btn-ghost:hover {
            background: rgba(255,255,255,.09);
            color: #fff;
        }
        .err-lock {
            width: 72px; height: 72px;
            margin: 0 auto 2rem;
            animation: pulse 2.5s ease-in-out infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50%       { opacity: .7; transform: scale(.95); }
        }
    </style>
</head>
<body>
    <a class="err-logo" href="{{ url('/') }}">
        <img src="{{ asset('images/logos/Wado Ticketing.png') }}" alt="Wado Tickets">
    </a>

    <svg class="err-lock" aria-hidden="true" viewBox="0 0 72 72" fill="none">
        <rect x="14" y="32" width="44" height="30" rx="7" fill="rgba(192,40,60,.18)" stroke="rgba(192,40,60,.5)" stroke-width="2"/>
        <path d="M24 32V24a12 12 0 0 1 24 0v8" stroke="rgba(192,40,60,.5)" stroke-width="2.5" stroke-linecap="round"/>
        <circle cx="36" cy="48" r="5" fill="rgba(192,40,60,.45)"/>
        <line x1="36" y1="52" x2="36" y2="57" stroke="rgba(192,40,60,.5)" stroke-width="2.5" stroke-linecap="round"/>
    </svg>

    <p class="err-code">403</p>
    <h1 class="err-title">Access denied</h1>
    <p class="err-sub">You don't have permission to view this page. If you think this is a mistake, please log in or contact support.</p>

    <div class="err-actions">
        <a href="{{ url('/') }}" class="err-btn err-btn-primary">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            Back to home
        </a>
        <a href="{{ route('login') }}" class="err-btn err-btn-ghost">
            Log in
        </a>
    </div>
</body>
</html>
