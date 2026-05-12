<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Event Approved — WADO Ticketing</title>
<style>
    body { margin:0; padding:0; background:#f4f1f1; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif; color:#2b1320; }
    .wrap { max-width:620px; margin:28px auto; background:#fff; border-radius:16px; overflow:hidden; box-shadow:0 10px 34px rgba(52,18,35,.14); }
    .header { padding:34px 36px 28px; background:linear-gradient(130deg,#6f1237 0%,#4e0f2d 55%,#320a1e 100%); color:#fff; }
    .brand { margin:0; font-size:1.28rem; font-weight:800; letter-spacing:.02em; }
    .subtitle { margin:8px 0 0; font-size:.92rem; color:rgba(255,255,255,.86); }
    .status { display:inline-block; margin-top:14px; background:#fde8f1; color:#5d1130; font-size:.72rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase; border-radius:999px; padding:6px 12px; }
    .body { padding:30px 36px 18px; }
    .greeting { margin:0 0 12px; font-size:1.02rem; color:#3a1328; }
    .intro { margin:0 0 20px; font-size:.95rem; line-height:1.7; color:#4d2a3c; }
    .event-card { background:linear-gradient(180deg,#fffdfd 0%,#faf5f7 100%); border:1px solid #ead7de; border-radius:12px; padding:18px 20px 14px; margin:0 0 20px; }
    .event-title { margin:0 0 12px; font-size:1.16rem; font-weight:800; color:#4d0f2f; }
    .row { display:flex; justify-content:space-between; gap:14px; border-bottom:1px dashed #e4ccd6; padding:8px 0; }
    .row:last-child { border-bottom:none; }
    .label { font-size:.75rem; text-transform:uppercase; letter-spacing:.06em; font-weight:700; color:#8e6077; }
    .value { font-size:.9rem; color:#4a2236; text-align:right; }
    .notice { background:#fff6fa; border:1px solid #f1ccd9; border-radius:12px; padding:14px 16px; margin:0 0 20px; }
    .notice p { margin:0; color:#6a1a3e; font-size:.9rem; line-height:1.65; }
    .section-title { margin:0 0 10px; font-size:.78rem; text-transform:uppercase; letter-spacing:.08em; color:#8e6077; }
    .steps { margin:0 0 20px; }
    .step { display:flex; gap:12px; margin:0 0 10px; }
    .step-num { width:24px; height:24px; flex-shrink:0; border-radius:50%; background:#651633; color:#fff; font-size:.75rem; font-weight:700; display:flex; align-items:center; justify-content:center; }
    .step-text { padding-top:2px; font-size:.89rem; line-height:1.58; color:#4d2a3c; }
    .panel-card { background:#fffdfd; border:1px solid #ead7de; border-radius:12px; padding:16px; margin:0 0 20px; }
    .panel-card p { margin:0 0 8px; font-size:.9rem; color:#4d2a3c; line-height:1.6; }
    .meta { margin:0; font-size:.87rem; color:#795166; }
    .meta strong { color:#4d0f2f; }
    .cta-wrap { margin:14px 0 6px; }
    .btn { display:inline-block; text-decoration:none; border-radius:10px; padding:11px 16px; font-size:.88rem; font-weight:700; }
    .btn-primary { background:#0f4bb6; color:#fff !important; box-shadow:0 6px 18px rgba(15,75,182,.25); }
    .btn-secondary { margin-left:8px; background:#ffffff; border:1px solid #dcb9c8; color:#651633 !important; }
    .footer { background:#faf5f7; border-top:1px solid #efdde4; padding:16px 36px 22px; }
    .footer p { margin:0; font-size:.77rem; color:#936a7d; line-height:1.62; }
    @media (max-width:520px) {
        .header,.body,.footer { padding-left:20px; padding-right:20px; }
        .row { flex-direction:column; align-items:flex-start; }
        .value { text-align:left; }
        .btn-secondary { margin-left:0; margin-top:8px; }
    }
</style>
</head>
<body>
<div class="wrap">
    <div class="header">
        <p class="brand">WADO Ticketing</p>
        <p class="subtitle">Event Approved</p>
        <span class="status">Approved & Live</span>
    </div>

    <div class="body">
        <p class="greeting">Hi {{ $owner->name }},</p>
        <p class="intro">
            Great news. Your event has been reviewed and approved on WADO Ticketing. Your listing is now eligible to appear to guests and start selling tickets.
        </p>

        <div class="event-card">
            <p class="event-title">{{ $event->title }}</p>
            <div class="row">
                <span class="label">Category</span>
                <span class="value">{{ $event->category?->name ?? 'Uncategorized' }}</span>
            </div>
            <div class="row">
                <span class="label">Venue</span>
                <span class="value">{{ $event->venue }}, {{ $event->city }}</span>
            </div>
            <div class="row">
                <span class="label">Starts</span>
                <span class="value">{{ optional($event->starts_at)->format('D, d M Y \a\t g:i A') }}</span>
            </div>
            <div class="row">
                <span class="label">Dashboard Alias</span>
                <span class="value">{{ $dashboardAlias }}</span>
            </div>
        </div>

        <div class="notice">
            <p>
                Your event owner dashboard access has been activated. Use your email (<strong>{{ $owner->email }}</strong>) to sign in and monitor performance in real time.
            </p>
        </div>

        <h3 class="section-title">Next Steps</h3>
        <div class="steps">
            <div class="step">
                <span class="step-num">1</span>
                <span class="step-text"><strong>Set your dashboard password:</strong> Click the "Set/Reset Password" button below, then create a secure password for dashboard login.</span>
            </div>
            <div class="step">
                <span class="step-num">2</span>
                <span class="step-text"><strong>Sign in to your event owner dashboard:</strong> Use your email and password to monitor tickets, sales, attendance and gate activity.</span>
            </div>
            <div class="step">
                <span class="step-num">3</span>
                <span class="step-text"><strong>Choose gate verification mode:</strong> If you want to self-manage, create your own verification agent from the dashboard's users/agents resource. If you prefer WADO-managed verification, our team can run gate checks while you monitor everything from your dashboard.</span>
            </div>
        </div>

        <div class="panel-card">
            <p class="meta"><strong>Dashboard login:</strong> {{ $dashboardLoginUrl }}</p>
            <p class="meta"><strong>Login email:</strong> {{ $owner->email }}</p>
            <div class="cta-wrap">
                <a href="{{ $setPasswordUrl }}" class="btn btn-primary">Set/Reset Password</a>
                <a href="{{ $dashboardLoginUrl }}" class="btn btn-secondary">Open Dashboard Login</a>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>
            This email was sent because your event was approved on WADO Ticketing.<br>
            &copy; {{ date('Y') }} WADO Ticketing. All rights reserved.
        </p>
    </div>
</div>
</body>
</html>
