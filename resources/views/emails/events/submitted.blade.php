<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Event Submitted — WADO Ticketing</title>
<style>
    body { margin:0; padding:0; background:#eceff5; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif; color:#0f172a; }
    .wrap { max-width:620px; margin:28px auto; background:#ffffff; border-radius:16px; overflow:hidden; box-shadow:0 8px 28px rgba(15,23,42,.12); }
    .header {
        padding:34px 36px 30px;
        background:linear-gradient(135deg,#1d4ed8 0%,#0f3a8f 55%,#1e3a8a 100%);
        color:#ffffff;
    }
    .brand { font-size:1.3rem; font-weight:800; letter-spacing:.02em; margin:0; }
    .subtitle { margin:8px 0 18px; color:rgba(255,255,255,.85); font-size:.9rem; }
    .status-pill {
        display:inline-block;
        font-size:.73rem;
        font-weight:700;
        text-transform:uppercase;
        letter-spacing:.08em;
        color:#1e3a8a;
        background:#dbeafe;
        padding:6px 12px;
        border-radius:999px;
    }
    .body { padding:32px 36px 20px; }
    .greeting { margin:0 0 12px; font-size:1.02rem; color:#111827; }
    .intro { margin:0 0 22px; line-height:1.72; font-size:.95rem; color:#334155; }
    .event-card {
        background:linear-gradient(180deg,#f8fbff 0%,#f6f8fc 100%);
        border:1px solid #dbe5f4;
        border-radius:14px;
        padding:20px 20px 14px;
        margin-bottom:24px;
    }
    .event-title { margin:0 0 14px; font-size:1.16rem; font-weight:800; color:#0f172a; letter-spacing:-.01em; }
    .detail-row {
        display:flex;
        justify-content:space-between;
        gap:14px;
        border-bottom:1px dashed #d6deea;
        padding:9px 0;
    }
    .detail-row:last-child { border-bottom:none; }
    .detail-label {
        font-size:.75rem;
        font-weight:700;
        color:#64748b;
        letter-spacing:.06em;
        text-transform:uppercase;
    }
    .detail-value { text-align:right; font-size:.9rem; color:#1f2937; line-height:1.45; }
    .notice {
        background:#eff6ff;
        border:1px solid #bfdbfe;
        border-radius:12px;
        padding:14px 16px;
        margin:0 0 24px;
    }
    .notice p { margin:0; font-size:.9rem; color:#1e3a8a; line-height:1.65; }
    .section-title {
        margin:0 0 12px;
        font-size:.78rem;
        font-weight:700;
        text-transform:uppercase;
        letter-spacing:.08em;
        color:#94a3b8;
    }
    .timeline { margin:0 0 24px; }
    .step {
        display:flex;
        gap:12px;
        align-items:flex-start;
        margin:0 0 11px;
    }
    .step-dot {
        width:24px;
        height:24px;
        border-radius:50%;
        background:#1d4ed8;
        color:#fff;
        font-size:.75rem;
        font-weight:700;
        display:flex;
        align-items:center;
        justify-content:center;
        flex-shrink:0;
    }
    .step-text { font-size:.89rem; color:#334155; line-height:1.58; padding-top:2px; }
    .support {
        margin:0;
        font-size:.87rem;
        color:#64748b;
        line-height:1.6;
    }
    .support a { color:#2563eb; text-decoration:none; }
    .footer {
        background:#f8fafc;
        border-top:1px solid #e2e8f0;
        padding:18px 36px 22px;
    }
    .footer p { margin:0; font-size:.77rem; color:#94a3b8; line-height:1.62; }
    @media (max-width:520px) {
        .header,.body,.footer { padding-left:20px; padding-right:20px; }
        .detail-row { flex-direction:column; align-items:flex-start; }
        .detail-value { text-align:left; }
    }
</style>
</head>
<body>
<div class="wrap">

    <div class="header">
        <p class="brand">WADO Ticketing</p>
        <p class="subtitle">Event Submission Confirmation</p>
        <span class="status-pill">Pending Review</span>
    </div>

    <div class="body">
        <p class="greeting">Hi {{ $user->name }},</p>
        <p class="intro">
            Thank you for submitting your event to WADO Ticketing. Your submission has been received successfully and is now awaiting review by our team.
        </p>

        <div class="event-card">
            <p class="event-title">{{ $event->title }}</p>

            <div class="detail-row">
                <span class="detail-label">Venue</span>
                <span class="detail-value">{{ $event->venue }}, {{ $event->city }}, {{ $event->country }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Date</span>
                <span class="detail-value">{{ $event->starts_at->format('D, d M Y \a\t g:i A') }}</span>
            </div>
            @if ($event->ends_at)
            <div class="detail-row">
                <span class="detail-label">Ends</span>
                <span class="detail-value">{{ $event->ends_at->format('D, d M Y \a\t g:i A') }}</span>
            </div>
            @endif
            <div class="detail-row">
                <span class="detail-label">Category</span>
                <span class="detail-value">{{ $event->category?->name ?? '—' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Ticket Types</span>
                <span class="detail-value">{{ $event->ticketCategories->count() }} {{ Str::plural('category', $event->ticketCategories->count()) }}</span>
            </div>
        </div>

        <div class="notice">
            <p>
                Your event is currently <strong>pending approval</strong>. After approval, it will appear on our platform and guests can begin purchasing tickets.
            </p>
        </div>

        <div class="timeline">
            <h3 class="section-title">What Happens Next</h3>

            <div class="step">
                <span class="step-dot">1</span>
                <span class="step-text"><strong>Review:</strong> Our team reviews your event details, usually within 1-2 business days.</span>
            </div>
            <div class="step">
                <span class="step-dot">2</span>
                <span class="step-text"><strong>Approval Update:</strong> You will receive a follow-up email once approved, or if we need more details.</span>
            </div>
            <div class="step">
                <span class="step-dot">3</span>
                <span class="step-text"><strong>Go Live:</strong> Once approved, your event is published and ticket sales can begin.</span>
            </div>
        </div>

        <p class="support">
            If you have any questions in the meantime, feel free to reach out to us at
            <a href="mailto:{{ config('mail.from.address') }}">{{ config('mail.from.address') }}</a>.
        </p>
    </div>

    <div class="footer">
        <p>
            You are receiving this email because you submitted an event on WADO Ticketing.<br>
            &copy; {{ date('Y') }} WADO Ticketing. All rights reserved.
        </p>
    </div>

</div>
</body>
</html>
