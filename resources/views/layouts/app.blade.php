<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Wado Events</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --site-width: 1140px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background: #060606;
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

        .site-footer {
            padding: 1.25rem 1rem 1.5rem;
            text-align: center;
            color: #b6b6b6;
            font-size: 0.92rem;
            background: #0e0e0f;
        }
    </style>
</head>
<body>
    <header class="site-header">
        @include('components.navbar')
    </header>

    <main class="page-content">
        @yield('content')
    </main>

    <footer class="site-footer">
        @include('components.footer')
    </footer>
</body>
</html>
