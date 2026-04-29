<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Server Error — Wado Events</title>
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
            max-width: 420px;
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
        .err-gears {
            position: relative;
            width: 110px;
            height: 110px;
            margin: 0 auto 2rem;
        }
        .gear-big {
            position: absolute;
            top: 0; left: 0;
            width: 72px; height: 72px;
            animation: spin 6s linear infinite;
            transform-origin: 36px 36px;
        }
        .gear-small {
            position: absolute;
            bottom: 0; right: 0;
            width: 50px; height: 50px;
            animation: spin 6s linear infinite reverse;
            transform-origin: 25px 25px;
        }
        @keyframes spin {
            from { transform: rotate(0deg); }
            to   { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <a class="err-logo" href="{{ url('/') }}">
        <img src="{{ asset('images/logos/logo-no-bg.png') }}" alt="Wado Events logo">
        <span class="err-logo-name">Wado Events</span>
    </a>

    <div class="err-gears" aria-hidden="true">
        <svg class="gear-big" viewBox="0 0 72 72" fill="none">
            <path d="M36 22a14 14 0 1 1 0 28 14 14 0 0 1 0-28z" stroke="rgba(192,40,60,.5)" stroke-width="2"/>
            <circle cx="36" cy="36" r="6" fill="rgba(192,40,60,.25)" stroke="rgba(192,40,60,.5)" stroke-width="1.5"/>
            <g stroke="rgba(192,40,60,.5)" stroke-width="3" stroke-linecap="round">
                <line x1="36" y1="8"  x2="36" y2="16"/>
                <line x1="36" y1="56" x2="36" y2="64"/>
                <line x1="8"  y1="36" x2="16" y2="36"/>
                <line x1="56" y1="36" x2="64" y2="36"/>
                <line x1="16.7" y1="16.7" x2="22.3" y2="22.3"/>
                <line x1="49.7" y1="49.7" x2="55.3" y2="55.3"/>
                <line x1="55.3" y1="16.7" x2="49.7" y2="22.3"/>
                <line x1="22.3" y1="49.7" x2="16.7" y2="55.3"/>
            </g>
        </svg>
        <svg class="gear-small" viewBox="0 0 50 50" fill="none">
            <path d="M25 15a10 10 0 1 1 0 20 10 10 0 0 1 0-20z" stroke="rgba(192,40,60,.35)" stroke-width="2"/>
            <circle cx="25" cy="25" r="4" fill="rgba(192,40,60,.18)" stroke="rgba(192,40,60,.35)" stroke-width="1.5"/>
            <g stroke="rgba(192,40,60,.35)" stroke-width="2.5" stroke-linecap="round">
                <line x1="25" y1="5"  x2="25" y2="12"/>
                <line x1="25" y1="38" x2="25" y2="45"/>
                <line x1="5"  y1="25" x2="12" y2="25"/>
                <line x1="38" y1="25" x2="45" y2="25"/>
                <line x1="11.5" y1="11.5" x2="16.5" y2="16.5"/>
                <line x1="33.5" y1="33.5" x2="38.5" y2="38.5"/>
                <line x1="38.5" y1="11.5" x2="33.5" y2="16.5"/>
                <line x1="16.5" y1="33.5" x2="11.5" y2="38.5"/>
            </g>
        </svg>
    </div>

    <p class="err-code">500</p>
    <h1 class="err-title">Something went wrong</h1>
    <p class="err-sub">Our server hit an unexpected snag. We've been notified and are looking into it. Please try again in a moment.</p>

    <div class="err-actions">
        <a href="{{ url('/') }}" class="err-btn err-btn-primary">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            Back to home
        </a>
        <button onclick="window.location.reload()" class="err-btn err-btn-ghost">
            Try again
        </button>
    </div>
</body>
</html>
