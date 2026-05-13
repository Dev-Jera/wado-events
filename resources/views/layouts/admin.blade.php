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
            <a href="{{ url('/dashboard') }}" class="admin-brand">Wado Admin</a>
            <nav class="admin-nav" aria-label="Admin navigation">
                <a href="{{ url('/dashboard') }}">Dashboard</a>
                <a href="{{ url('/dashboard/events/create') }}">Create event</a>
                <a href="{{ url('/dashboard/categories') }}">Categories</a>
                <a href="{{ route('payments.admin.index') }}">Payment Monitor</a>
                <a href="{{ route('admin.finance.index') }}">Finance</a>
                <a href="{{ route('gate.portal') }}">Gate Portal</a>
                <a href="{{ url('/dashboard/users') }}">Users & Roles</a>
                <a href="{{ route('events.index') }}">Public events</a>
                <a href="{{ url('/dashboard/content-management') }}">Content Management</a>
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

    <script>
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
