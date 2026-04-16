<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Wado Events</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0a4fbe">
    <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">

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
            /* base fallback — home page overrides with its own gradient */
            background: #110608;
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

        .site-footer {
            padding: 1.25rem 1rem 1.5rem;
            text-align: center;
            color: rgba(180, 200, 240, .55);
            font-size: 0.92rem;
            background: rgba(255,255,255,.04);
            border-top: 1px solid rgba(255,255,255,.07);
        }
    </style>
</head>
<body>
    @php($isFullBleed = trim((string) $__env->yieldContent('fullbleed')) === '1')

    @unless($isFullBleed)
        <header class="site-header">
            @include('components.navbar')
        </header>
    @endunless

    <main class="page-content">
        @if (session('success'))
            <div class="site-flash {{ $isFullBleed ? 'site-flash-fullbleed' : '' }} site-flash-success">{{ session('success') }}</div>
        @endif
        @if (session('warning'))
            <div class="site-flash {{ $isFullBleed ? 'site-flash-fullbleed' : '' }} site-flash-warning">{{ session('warning') }}</div>
        @endif
        @yield('content')
    </main>

    @unless($isFullBleed)
        <footer class="site-footer">
            @include('components.footer')
        </footer>
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
