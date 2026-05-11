<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'WADO Ticketing')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#c0283c">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/logos/Favcon Wado Ticketing.png') }}">
    <link rel="shortcut icon" href="{{ asset('images/logos/Favcon Wado Ticketing.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logos/Wado Ticketing.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">

    {{-- Hide page until all styles are parsed — prevents flash of unstyled content --}}
    <style>html{opacity:0;}</style>

    {{-- Warm up font connections before anything else --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Reveal page with a fade once all render-blocking styles are applied --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.documentElement.style.transition = 'opacity 0.18s ease';
            document.documentElement.style.opacity   = '1';
        });
    </script>

    <style>
        :root {
            --site-width: 1140px;
            --site-font: 'Quicksand', 'Nunito', 'Plus Jakarta Sans', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: var(--site-font);
            background: #2a1015;
            color: #f4f4f4;
        }

        .site-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 40;
            padding: 1.15rem 1rem;
            pointer-events: none;
        }

        .site-header > * {
            pointer-events: auto;
        }

        .page-content {
            min-height: 100vh;
        }

        .site-flash {
            position: fixed;
            top: 6.5rem;
            left: 50%;
            transform: translateX(-50%);
            z-index: 45;
            width: min(560px, calc(100% - 2rem));
            padding: 0.95rem 1.1rem;
            border-radius: 16px;
            box-shadow: 0 18px 38px rgba(0, 0, 0, 0.28);
        }

        .site-flash-success {
            background: #e8fff3;
            color: #0a7f4f;
            border: 1px solid #9ce1be;
        }

        .site-flash-warning {
            background: #fff6e5;
            color: #9a6700;
            border: 1px solid #f4d28c;
        }

        .site-flash-error {
            background: #fff1f0;
            color: #b91c1c;
            border: 1px solid #fca5a5;
        }

        .site-flash-info {
            background: #fff1f3;
            color: #8a1525;
            border: 1px solid #f0a0a8;
        }

        /* ── Policy pages ─────────────────────────────── */
        .policy-page { min-height: 70vh; background: #fff; }

        /* Hero — solid red */
        .policy-hero {
            background: #c0283c;
            padding: 4rem 1.25rem 3rem;
            text-align: center;
        }
        .policy-hero-inner { max-width: 700px; margin: 0 auto; }
        .policy-eyebrow {
            font-size: .7rem; letter-spacing: .2em; text-transform: uppercase;
            color: rgba(255,255,255,.75); font-weight: 700; margin-bottom: .6rem;
        }
        .policy-heading {
            font-size: clamp(1.8rem, 4vw, 2.8rem);
            font-weight: 800; color: #fff; margin: 0 0 .75rem; line-height: 1.15;
        }
        .policy-sub { color: rgba(255,255,255,.7); font-size: .9rem; margin: 0; }

        /* Shell layout */
        .policy-shell {
            max-width: var(--site-width, 1140px);
            margin: 0 auto;
            padding: 3rem 1.25rem 4rem;
            display: grid;
            grid-template-columns: 220px 1fr;
            gap: 3rem;
            align-items: start;
        }

        /* TOC sidebar — red accent border */
        .policy-toc {
            position: sticky; top: 90px;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-left: 4px solid #c0283c;
            border-radius: 8px;
            padding: 1.25rem 1rem;
        }
        .policy-toc-title {
            font-size: .68rem; letter-spacing: .15em; text-transform: uppercase;
            color: #c0283c; font-weight: 700; margin: 0 0 .85rem .1rem;
        }
        .policy-toc nav { display: flex; flex-direction: column; gap: .25rem; }
        .policy-toc nav a {
            color: #1e3a8a; text-decoration: none;
            font-size: .84rem; padding: .35rem .6rem; border-radius: 5px;
            transition: background .15s, color .15s; display: block;
        }
        .policy-toc nav a:hover { background: #fef2f4; color: #c0283c; }

        /* Article body — white bg, dark text */
        .policy-body section {
            margin-bottom: 2.5rem; padding-bottom: 2.5rem;
            border-bottom: 1px solid #e5e7eb;
        }
        .policy-body section:last-child { border-bottom: none; margin-bottom: 0; }
        .policy-body h2 {
            font-size: 1.15rem; font-weight: 700;
            color: #c0283c; margin: 0 0 .9rem;
        }
        .policy-body p {
            color: #374151; line-height: 1.8; margin-bottom: .9rem; font-size: .95rem;
        }
        .policy-body p:last-child { margin-bottom: 0; }
        .policy-body ul { margin: .5rem 0 1rem 1.25rem; display: flex; flex-direction: column; gap: .45rem; }
        .policy-body ul li { color: #4b5563; line-height: 1.7; font-size: .93rem; }
        .policy-body strong { color: #111827; }
        .policy-body a { color: #1e3a8a; text-decoration: none; font-weight: 600; }
        .policy-body a:hover { text-decoration: underline; color: #c0283c; }

        @media (max-width: 760px) {
            .policy-shell { grid-template-columns: 1fr; gap: 1.5rem; }
            .policy-toc { position: static; }
        }
    </style>

    @stack('styles')
</head>
<body>
    @php($isFullBleed = trim((string) $__env->yieldContent('fullbleed')) === '1')

    @unless($isFullBleed)
        <header class="site-header">
            @include('components.navbar')
        </header>
    @endunless

    <main class="page-content">
        @foreach (['success' => 'site-flash-success', 'warning' => 'site-flash-warning', 'error' => 'site-flash-error', 'info' => 'site-flash-info'] as $key => $cls)
            @if (session($key))
                <div class="site-flash {{ $cls }}">{{ session($key) }}</div>
            @endif
        @endforeach
        @yield('content')
    </main>

    @unless($isFullBleed)
        @include('components.footer')
    @endunless

    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function () {
                navigator.serviceWorker.register('{{ asset('sw.js') }}').catch(function () {});
            });
        }
    </script>
</body>
</html>
