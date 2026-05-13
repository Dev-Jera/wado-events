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
        .policy-page { min-height: 70vh; background: #f9f5f5; padding-top: 3rem; }

        /* Hero — clean minimal design */
        .policy-hero {
            background: #f9f5f5;
            padding: 3.5rem 1.25rem 2.5rem;
            text-align: left;
            border-bottom: 1px solid #e8dedd;
        }
        .policy-hero-inner { max-width: var(--site-width, 1140px); margin: 0 auto; }
        .policy-eyebrow {
            font-size: .65rem; letter-spacing: .25em; text-transform: uppercase;
            color: #8b1a1a; font-weight: 800; margin-bottom: .4rem;
        }
        .policy-heading {
            font-size: clamp(2rem, 5vw, 3rem);
            font-weight: 800; color: #8b1a1a; margin: 0 0 .5rem; line-height: 1.2;
        }
        .policy-sub { color: #8b8b8b; font-size: .85rem; margin: 0; }

        /* Shell layout */
        .policy-shell {
            max-width: var(--site-width, 1140px);
            margin: 0 auto;
            padding: 3rem 1.25rem 4rem;
            display: grid;
            grid-template-columns: 260px 1fr;
            gap: 3rem;
            align-items: start;
        }

        /* TOC sidebar — blue accent border (reference style) */
        .policy-toc {
            position: sticky; top: 90px;
            background: #fff;
            border: 1px solid #e8dedd;
            border-left: 4px solid #0088cc;
            border-radius: 6px;
            padding: 1.5rem 1.25rem;
            margin-bottom: 2rem;
        }
        .policy-toc-title {
            font-size: .65rem; letter-spacing: .2em; text-transform: uppercase;
            color: #8b1a1a; font-weight: 800; margin: 0 0 1rem .1rem;
        }
        .policy-toc nav { display: flex; flex-direction: column; gap: .5rem; }
        .policy-toc nav a {
            color: #555; text-decoration: none;
            font-size: .9rem; padding: .4rem .8rem; border-radius: 4px;
            transition: all .15s; display: block;
        }
        .policy-toc nav a:hover { background: #f0e8e8; color: #8b1a1a; }

        /* Article body — white bg, dark text */
        .policy-body section {
            margin-bottom: 2.2rem; padding-bottom: 2.2rem;
            border-bottom: 1px solid #e8dedd;
        }
        .policy-body section:last-child { border-bottom: none; margin-bottom: 0; }
        .policy-body h2 {
            font-size: 1.1rem; font-weight: 700;
            color: #333; margin: 0 0 1rem;
        }
        .policy-body p {
            color: #555; line-height: 1.8; margin-bottom: .9rem; font-size: .95rem;
        }
        .policy-body p:last-child { margin-bottom: 0; }
        .policy-body ul { margin: .6rem 0 1rem 1.5rem; display: flex; flex-direction: column; gap: .5rem; }
        .policy-body ul li { color: #555; line-height: 1.7; font-size: .95rem; }
        .policy-body strong { color: #222; }
        .policy-body a { color: #0088cc; text-decoration: none; font-weight: 600; }
        .policy-body a:hover { text-decoration: underline; color: #8b1a1a; }

        @media (max-width: 760px) {
            .policy-hero { padding: 2.5rem 1.25rem 1.75rem; }
            .policy-shell { grid-template-columns: 1fr; gap: 1.5rem; }
            .policy-toc { position: static; }
            .policy-heading { font-size: clamp(1.5rem, 4vw, 2rem); }
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

        (function () {
            const strictEmailPattern = /^[^\s@]+@(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z]{2,63}$/i;
            const strictPhonePattern = /^\+?[1-9]\d{7,14}$/;

            function cleanControlChars(value) {
                return value.replace(/[\u0000-\u001F\u007F]/g, '');
            }

            function normalizeValue(field) {
                const type = (field.getAttribute('type') || '').toLowerCase();
                if (field.tagName !== 'INPUT' && field.tagName !== 'TEXTAREA') return;
                if (['file', 'hidden', 'password'].includes(type)) return;

                let value = cleanControlChars(field.value || '');
                if (type === 'email') {
                    value = value.trim().toLowerCase();
                } else if (type === 'tel') {
                    value = value.trim().replace(/[^\d+]/g, '').replace(/(?!^)\+/g, '');
                } else {
                    value = value.trim();
                }

                field.value = value;
            }

            function applyDefaults(field) {
                const type = (field.getAttribute('type') || '').toLowerCase();

                if (field.tagName === 'TEXTAREA' && !field.hasAttribute('maxlength')) {
                    field.setAttribute('maxlength', '5000');
                }

                if (field.tagName === 'INPUT') {
                    if (type === 'email') {
                        field.setAttribute('inputmode', 'email');
                        field.setAttribute('spellcheck', 'false');
                        field.setAttribute('autocapitalize', 'none');
                        if (!field.hasAttribute('maxlength')) field.setAttribute('maxlength', '255');
                    }

                    if (type === 'tel') {
                        field.setAttribute('inputmode', 'numeric');
                        if (!field.hasAttribute('maxlength')) field.setAttribute('maxlength', '16');
                        if (!field.hasAttribute('pattern')) field.setAttribute('pattern', '^\\+?[1-9]\\d{7,14}$');
                    }

                    if (['text', 'search', 'url'].includes(type) && !field.hasAttribute('maxlength')) {
                        field.setAttribute('maxlength', '255');
                    }
                }
            }

            function validateField(field) {
                const type = (field.getAttribute('type') || '').toLowerCase();
                const value = (field.value || '').trim();
                field.setCustomValidity('');

                if (type === 'email' && value !== '' && !strictEmailPattern.test(value)) {
                    field.setCustomValidity('Please enter a valid email address.');
                }

                if (type === 'tel' && value !== '' && !strictPhonePattern.test(value)) {
                    field.setCustomValidity('Please enter a valid phone number in international format, for example +256700000000.');
                }
            }

            document.addEventListener('DOMContentLoaded', function () {
                const forms = document.querySelectorAll('form:not([data-sanitize="off"])');

                forms.forEach((form) => {
                    const fields = form.querySelectorAll('input, textarea');

                    fields.forEach((field) => {
                        applyDefaults(field);

                        field.addEventListener('blur', function () {
                            normalizeValue(field);
                            validateField(field);
                        });
                    });

                    form.addEventListener('submit', function (event) {
                        let invalid = false;

                        fields.forEach((field) => {
                            normalizeValue(field);
                            validateField(field);
                            if (!field.checkValidity()) invalid = true;
                        });

                        if (invalid) {
                            event.preventDefault();
                            form.reportValidity();
                        }
                    });
                });
            });
        })();
    </script>
</body>
</html>
