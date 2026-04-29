<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Page Not Found — Wado Events</title>
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
        .err-logo img { height: 36px; }
        .err-logo-name { font-size: 1.15rem; font-weight: 700; }
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
        .err-ticket {
            position: relative;
            width: 180px;
            height: 100px;
            margin: 0 auto 2rem;
            animation: float 4s ease-in-out infinite;
        }
        .err-ticket svg { width: 100%; height: 100%; }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50%       { transform: translateY(-12px); }
        }
    </style>
</head>
<body>
    <a class="err-logo" href="{{ url('/') }}">
        <img src="{{ asset('images/logos/logo-no-bg.png') }}" alt="Wado Events logo">
        <span class="err-logo-name">Wado Events</span>
    </a>

    <div class="err-ticket" aria-hidden="true">
        <svg viewBox="0 0 180 100" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="8" y="18" width="164" height="64" rx="10" fill="rgba(192,40,60,.18)" stroke="rgba(192,40,60,.45)" stroke-width="1.5"/>
            <path d="M120 18v18a12 12 0 0 1 0 28v18" stroke="rgba(192,40,60,.5)" stroke-width="1.5" stroke-dasharray="4 3"/>
            <circle cx="120" cy="50" r="10" fill="rgba(21,5,8,1)" stroke="rgba(192,40,60,.45)" stroke-width="1.5"/>
            <text x="52" y="44" font-family="monospace" font-size="9" fill="rgba(255,215,220,.4)" letter-spacing="1">WADO EVENTS</text>
            <text x="28" y="62" font-family="monospace" font-size="14" font-weight="700" fill="rgba(255,255,255,.18)" letter-spacing="2">404</text>
            <line x1="28" y1="70" x2="104" y2="70" stroke="rgba(255,255,255,.08)" stroke-width="1" stroke-dasharray="3 3"/>
        </svg>
    </div>

    <p class="err-code">404</p>
    <h1 class="err-title">Page not found</h1>
    <p class="err-sub">The page you're looking for doesn't exist or may have been moved. Let's get you back on track.</p>

    <div class="err-actions">
        <a href="{{ url('/') }}" class="err-btn err-btn-primary">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            Back to home
        </a>
        <a href="{{ route('events.index') }}" class="err-btn err-btn-ghost">
            Browse events
        </a>
    </div>
</body>
</html>
