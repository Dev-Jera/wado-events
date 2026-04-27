<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>New Package Enquiry</title>
<style>
    body { margin: 0; padding: 0; background: #f4f4f5; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }
    .wrap { max-width: 600px; margin: 32px auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,.08); }
    .header { background: #c0283c; padding: 32px 36px; }
    .header h1 { margin: 0; color: #fff; font-size: 1.35rem; font-weight: 700; }
    .header p  { margin: 6px 0 0; color: rgba(255,255,255,.78); font-size: 0.88rem; }
    .body { padding: 32px 36px; }
    .badge { display: inline-block; background: #fef3f2; border: 1px solid #fecaca; color: #c0283c; font-size: 0.75rem; font-weight: 700; letter-spacing: 0.06em; text-transform: uppercase; border-radius: 999px; padding: 4px 12px; margin-bottom: 24px; }
    .row { display: flex; gap: 12px; margin-bottom: 14px; }
    .label { min-width: 160px; font-size: 0.8rem; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: 0.06em; padding-top: 2px; }
    .value { font-size: 0.95rem; color: #111827; line-height: 1.5; }
    .divider { border: none; border-top: 1px solid #f3f4f6; margin: 24px 0; }
    .message-box { background: #f9fafb; border-radius: 8px; padding: 16px 18px; margin-top: 8px; }
    .message-box p { margin: 0; font-size: 0.93rem; color: #374151; line-height: 1.65; white-space: pre-wrap; }
    .footer { background: #f9fafb; padding: 20px 36px; border-top: 1px solid #f3f4f6; }
    .footer p { margin: 0; font-size: 0.8rem; color: #9ca3af; }
    @media (max-width: 480px) {
        .body, .header, .footer { padding-left: 20px; padding-right: 20px; }
        .row { flex-direction: column; gap: 2px; }
        .label { min-width: unset; }
    }
</style>
</head>
<body>
<div class="wrap">
    <div class="header">
        <h1>New Package Enquiry</h1>
        <p>Submitted via the WADO Events website</p>
    </div>
    <div class="body">
        <div class="badge">{{ $data['package'] }}</div>

        <div class="row">
            <span class="label">Name</span>
            <span class="value">{{ $data['name'] }}</span>
        </div>
        <div class="row">
            <span class="label">Email</span>
            <span class="value"><a href="mailto:{{ $data['email'] }}" style="color:#c0283c;">{{ $data['email'] }}</a></span>
        </div>
        @if (!empty($data['phone']))
        <div class="row">
            <span class="label">Phone</span>
            <span class="value">{{ $data['phone'] }}</span>
        </div>
        @endif
        @if (!empty($data['event_date']))
        <div class="row">
            <span class="label">Event Date</span>
            <span class="value">{{ \Carbon\Carbon::parse($data['event_date'])->format('d M Y') }}</span>
        </div>
        @endif
        @if (!empty($data['attendance']))
        <div class="row">
            <span class="label">Expected Attendance</span>
            <span class="value">{{ $data['attendance'] }}</span>
        </div>
        @endif

        @if (!empty($data['message']))
        <hr class="divider">
        <div class="row" style="flex-direction:column; gap:6px;">
            <span class="label">Additional Details</span>
            <div class="message-box"><p>{{ $data['message'] }}</p></div>
        </div>
        @endif
    </div>
    <div class="footer">
        <p>Reply directly to this email to respond to {{ $data['name'] }}.</p>
    </div>
</div>
</body>
</html>
