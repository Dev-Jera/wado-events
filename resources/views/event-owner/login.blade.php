<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Owner Dashboard Login - WADO Ticketing</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Quicksand','Nunito','Plus Jakarta Sans','Segoe UI',system-ui,sans-serif;
            background: linear-gradient(135deg, #f4f1f1 0%, #f9f7f8 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            color: #2b1320;
        }
        .login-container {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(52, 18, 35, 0.15);
            width: 100%;
            max-width: 420px;
            overflow: hidden;
        }
        .login-header {
            background: linear-gradient(130deg, #6f1237 0%, #4e0f2d 55%, #320a1e 100%);
            color: #ffffff;
            padding: 40px 32px;
            text-align: center;
        }
        .login-header h1 {
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 8px;
            letter-spacing: -0.01em;
        }
        .login-header p {
            font-size: 0.95rem;
            color: rgba(255, 255, 255, 0.9);
            font-weight: 500;
            margin-bottom: 16px;
        }
        .event-name {
            background: rgba(255, 255, 255, 0.15);
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 600;
            word-break: break-word;
        }
        .login-body {
            padding: 40px 32px;
        }
        .form-group {
            margin-bottom: 24px;
        }
        .form-group label {
            display: block;
            font-size: 0.9rem;
            font-weight: 600;
            color: #3a1328;
            margin-bottom: 8px;
        }
        .form-group input {
            width: 100%;
            padding: 12px 14px;
            font-size: 0.95rem;
            border: 1px solid #dcc3cc;
            border-radius: 10px;
            font-family: inherit;
            transition: border-color 0.2s;
        }
        .form-group input:focus {
            outline: none;
            border-color: #651633;
            box-shadow: 0 0 0 3px rgba(101, 22, 51, 0.1);
        }
        .form-group input::placeholder {
            color: #a89a9f;
        }
        .submit-btn {
            width: 100%;
            padding: 12px 16px;
            font-size: 0.95rem;
            font-weight: 700;
            color: #ffffff;
            background: #0f4bb6;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: background 0.2s;
            font-family: inherit;
            box-shadow: 0 6px 18px rgba(15, 75, 182, 0.25);
        }
        .submit-btn:hover {
            background: #0d3d8e;
        }
        .submit-btn:active {
            transform: scale(0.98);
        }
        .error-message {
            background: #fde8e8;
            border: 1px solid #f1ccd9;
            border-radius: 8px;
            padding: 12px 14px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            color: #8b1a3e;
            font-weight: 500;
        }
        .success-message {
            background: #e8f5e9;
            border: 1px solid #c3e9c3;
            border-radius: 8px;
            padding: 12px 14px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            color: #2d6a2d;
            font-weight: 500;
        }
        .form-footer {
            text-align: center;
            margin-top: 20px;
            font-size: 0.9rem;
            color: #795166;
        }
        .form-footer a {
            color: #0f4bb6;
            text-decoration: none;
            font-weight: 600;
        }
        .form-footer a:hover {
            text-decoration: underline;
        }
        .error-input {
            border-color: #d92a4a !important;
        }
        @media (max-width: 480px) {
            .login-container { margin: 0; }
            .login-header { padding: 32px 24px; }
            .login-body { padding: 32px 24px; }
            .login-header h1 { font-size: 1.2rem; }
        }
    </style>
</head>
<body>
<div class="login-container">
    <div class="login-header">
        <h1>Event Dashboard</h1>
        <p>Access your event monitoring dashboard</p>
        <div class="event-name">{{ $event->title }}</div>
    </div>

    <div class="login-body">
        @if ($errors->any())
            @foreach ($errors->all() as $error)
                <div class="error-message">{{ $error }}</div>
            @endforeach
        @endif

        @if (session('success'))
            <div class="success-message">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('owner.dashboard-login', ['eventSlug' => $eventSlug]) }}">
            @csrf

            <div class="form-group">
                <label for="email">Email Address</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="your@email.com"
                    required
                    autofocus
                    autocomplete="email"
                    inputmode="email"
                    spellcheck="false"
                    autocapitalize="none"
                    class="@error('email') error-input @enderror"
                >
                @error('email')
                    <span style="font-size:.8rem;color:#d92a4a;font-weight:500;">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Enter your password"
                    required
                    class="@error('password') error-input @enderror"
                >
                @error('password')
                    <span style="font-size:.8rem;color:#d92a4a;font-weight:500;">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="submit-btn">Sign In to Dashboard</button>
        </form>

        <div class="form-footer">
            <p>Having trouble? <a href="{{ route('contact') }}">Contact Support</a></p>
        </div>
    </div>
</div>
<script>
    (function () {
        const form = document.querySelector('form');
        const emailInput = document.querySelector('input[name="email"]');
        const strictEmailPattern = /^[^\s@]+@(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z]{2,63}$/i;

        if (!form || !emailInput) return;

        function normalizeEmail() {
            emailInput.value = (emailInput.value || '')
                .replace(/[\u0000-\u001F\u007F]/g, '')
                .trim()
                .toLowerCase();
        }

        emailInput.addEventListener('blur', function () {
            normalizeEmail();
            emailInput.setCustomValidity('');
            if (emailInput.value !== '' && !strictEmailPattern.test(emailInput.value)) {
                emailInput.setCustomValidity('Please enter a valid email address.');
            }
        });

        form.addEventListener('submit', function (event) {
            normalizeEmail();
            emailInput.setCustomValidity('');
            if (emailInput.value !== '' && !strictEmailPattern.test(emailInput.value)) {
                emailInput.setCustomValidity('Please enter a valid email address.');
                event.preventDefault();
                form.reportValidity();
            }
        });
    })();
</script>
</body>
</html>
