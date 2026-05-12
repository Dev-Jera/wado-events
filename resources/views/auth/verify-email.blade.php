<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email - WADO Events</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body style="margin:0;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:32px 16px;background:#f4f1f1;font-family:'Quicksand','Nunito','Plus Jakarta Sans','Segoe UI',system-ui,sans-serif;color:#2b1320;">
    <section style="width:min(520px,100%);background:#fff;border-radius:18px;box-shadow:0 10px 34px rgba(52,18,35,.14);border:1px solid #f0dde0;overflow:hidden;">
        <div style="background:linear-gradient(130deg,#6f1237 0%,#4e0f2d 55%,#320a1e 100%);padding:30px 32px;text-align:center;color:#fff;">
            <h1 style="margin:0;font-size:1.3rem;font-weight:700;">Check your inbox</h1>
            <p style="margin:10px 0 0;color:rgba(255,255,255,.86);font-size:.9rem;font-weight:500;line-height:1.5;">
                We sent a verification link to<br>
                <strong>{{ auth()->user()->email }}</strong>
            </p>
        </div>

        <div style="padding:26px 32px 30px;">
            @if (session('status'))
                <div style="background:#edf9f2;border:1px solid #bde8cb;border-radius:10px;padding:12px 14px;margin-bottom:18px;color:#136f45;font-size:.86rem;font-weight:600;">
                    {{ session('status') }}
                </div>
            @endif

            <p style="margin:0 0 18px;color:#4d2a3c;font-size:.9rem;line-height:1.68;font-weight:500;">
                Click the verification link in your email to activate your account. Once verified, ticket and payment notifications will be delivered without interruption.
            </p>

            <p style="margin:0 0 6px;color:#8e6077;font-size:.78rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;">Did not get it?</p>
            <p style="margin:0 0 16px;color:#795166;font-size:.84rem;font-weight:500;">Check spam or resend using the button below.</p>

            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" style="width:100%;border:0;border-radius:10px;background:#0f4bb6;color:#fff;font-size:.9rem;font-weight:700;padding:12px;cursor:pointer;font-family:inherit;box-shadow:0 6px 18px rgba(15,75,182,.25);">
                    Resend Verification Email
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}" style="margin-top:10px;">
                @csrf
                <button type="submit" style="width:100%;border:1px solid #e4ccd6;border-radius:10px;background:#fff;color:#651633;font-size:.86rem;font-weight:600;padding:11px;cursor:pointer;font-family:inherit;">
                    Log Out
                </button>
            </form>

            <a href="{{ route('home') }}" style="display:block;margin-top:10px;text-align:center;text-decoration:none;border:1px solid #d7e3f7;border-radius:10px;background:#f7fbff;color:#0f4bb6;font-size:.86rem;font-weight:600;padding:11px;font-family:inherit;">
                Back to Home
            </a>
        </div>
    </section>
</body>
</html>
