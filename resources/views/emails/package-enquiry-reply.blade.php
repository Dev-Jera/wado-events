<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reply from WADO Events</title>
<style>
    body { margin:0; padding:0; background:#f4f4f5; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif; }
    .wrap { max-width:600px; margin:32px auto; background:#fff; border-radius:12px; overflow:hidden; box-shadow:0 2px 12px rgba(0,0,0,.08); }
    .header { background:#c0283c; padding:32px 36px; }
    .header h1 { margin:0; color:#fff; font-size:1.3rem; font-weight:700; }
    .header p  { margin:6px 0 0; color:rgba(255,255,255,.78); font-size:.88rem; }
    .body { padding:32px 36px; }
    .greeting { font-size:1rem; color:#111827; margin:0 0 20px; }
    .reply-box { background:#f9fafb; border-left:4px solid #c0283c; border-radius:0 8px 8px 0; padding:18px 20px; margin-bottom:24px; }
    .reply-box p { margin:0; font-size:.95rem; color:#374151; line-height:1.7; white-space:pre-wrap; }
    .divider { border:none; border-top:1px solid #f3f4f6; margin:24px 0; }
    .original-label { font-size:.75rem; font-weight:700; letter-spacing:.06em; text-transform:uppercase; color:#9ca3af; margin:0 0 12px; }
    .row { display:flex; gap:12px; margin-bottom:10px; }
    .label { min-width:140px; font-size:.8rem; font-weight:700; color:#6b7280; padding-top:1px; }
    .value { font-size:.9rem; color:#374151; line-height:1.5; }
    .footer { background:#f9fafb; padding:20px 36px; border-top:1px solid #f3f4f6; }
    .footer p { margin:0; font-size:.78rem; color:#9ca3af; line-height:1.55; }
    @media (max-width:480px) {
        .body,.header,.footer { padding-left:20px; padding-right:20px; }
        .row { flex-direction:column; gap:2px; }
        .label { min-width:unset; }
    }
</style>
</head>
<body>
<div class="wrap">
    <div class="header">
        <h1>WADO Events</h1>
        <p>Response to your package enquiry</p>
    </div>
    <div class="body">
        <p class="greeting">Hi {{ $enquiry->name }},</p>
        <p style="margin:0 0 18px;font-size:.93rem;color:#374151;">Thank you for reaching out about our <strong>{{ $enquiry->package }}</strong> package. Here is our response:</p>

        <div class="reply-box">
            <p>{{ $replyMessage }}</p>
        </div>

        <hr class="divider">

        <p class="original-label">Your original enquiry</p>
        <div class="row">
            <span class="label">Package</span>
            <span class="value">{{ $enquiry->package }}</span>
        </div>
        @if ($enquiry->event_date)
        <div class="row">
            <span class="label">Event Date</span>
            <span class="value">{{ $enquiry->event_date->format('d M Y') }}</span>
        </div>
        @endif
        @if ($enquiry->attendance)
        <div class="row">
            <span class="label">Attendance</span>
            <span class="value">{{ $enquiry->attendance }}</span>
        </div>
        @endif
        @if ($enquiry->message)
        <div class="row">
            <span class="label">Your message</span>
            <span class="value">{{ $enquiry->message }}</span>
        </div>
        @endif
    </div>
    <div class="footer">
        <p>This email was sent in response to an enquiry submitted on the WADO Events website.<br>
        To continue the conversation, reply directly to this email.</p>
    </div>
</div>
</body>
</html>
