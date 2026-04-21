<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ticket PDF</title>
</head>
<body>
@php
    $isCancelled = $ticket->status === \App\Models\Ticket::STATUS_CANCELLED;
    $isUsed = $ticket->status === \App\Models\Ticket::STATUS_USED;
    $statusLabel = $isCancelled ? 'Cancelled' : ($isUsed ? 'Used' : 'Confirmed');
    $statusClass = $isCancelled ? 'danger' : ($isUsed ? 'muted' : 'success');
    $useEventImage = !empty($eventImageUri ?? null);
@endphp

<div class="page">
    <div class="ticket {{ $isCancelled ? 'ticket-cancelled' : '' }}">
        <div class="top-band"></div>

        <table class="hero" cellpadding="0" cellspacing="0">
            <tr>
                <td class="hero-copy">
                    <div class="status-pill {{ $statusClass }}">{{ $statusLabel }}</div>
                    <h1>{{ $ticket->event->title }}</h1>
                    <p class="desc">{{ $ticket->event->description ? \Illuminate\Support\Str::limit($ticket->event->description, 145) : 'Plan your trip and keep your ticket details ready for check-in at the venue.' }}</p>

                    <table class="meta" cellpadding="0" cellspacing="0">
                        <tr>
                            <td>{{ $ticket->event->starts_at->format('d M Y') }}</td>
                            <td>{{ $ticket->event->starts_at->format('H:i') }}</td>
                            <td>{{ $ticket->event->city }}</td>
                        </tr>
                    </table>
                </td>
                <td class="hero-image">
                    @if ($useEventImage)
                        <img src="{{ $eventImageUri }}" alt="{{ $ticket->event->title }}">
                    @else
                        <div class="image-fallback">{{ $ticket->ticketCategory->name }}</div>
                    @endif
                </td>
            </tr>
        </table>

        <div class="strip">
            <span class="code">{{ $ticket->ticket_code }}</span>
            <span class="type">{{ $ticket->ticketCategory->name }} · {{ $ticket->quantity }} ticket{{ $ticket->quantity > 1 ? 's' : '' }}</span>
        </div>

        <table class="details" cellpadding="0" cellspacing="0">
            <tr>
                <td><span class="label">Ticket code</span><strong class="mono">{{ $ticket->ticket_code }}</strong></td>
                <td><span class="label">Category</span><strong>{{ $ticket->ticketCategory->name }}</strong></td>
                <td><span class="label">Quantity</span><strong>{{ $ticket->quantity }} ticket{{ $ticket->quantity > 1 ? 's' : '' }}</strong></td>
                <td><span class="label">Venue</span><strong>{{ $ticket->event->venue }}</strong></td>
            </tr>
            <tr>
                <td><span class="label">City</span><strong>{{ $ticket->event->city }}</strong></td>
                <td><span class="label">Purchased</span><strong>{{ $ticket->purchased_at->format('d M Y') }}</strong></td>
                <td><span class="label">Payment</span><strong>{{ $ticket->payment_provider === 'free' ? 'Free' : strtoupper((string) $ticket->payment_provider) }}</strong></td>
                <td><span class="label">Status</span><strong>{{ ucfirst($ticket->status) }}</strong></td>
            </tr>
        </table>

        <table class="qr-row" cellpadding="0" cellspacing="0">
            <tr>
                <td class="qr-cell">
                    <div class="qr-box">
                        @if (!empty($qrCodeDataUri))
                            <img src="{{ $qrCodeDataUri }}" alt="QR Code">
                        @else
                            <div class="qr-placeholder">QR</div>
                        @endif
                    </div>
                </td>
                <td class="scan-copy">
                    <div class="scan-title">Scan at entry</div>
                    <div class="scan-sub">Present this QR code at the venue gate. Screenshot or print for offline use.</div>
                </td>
                <td class="zone-cell">
                    <div class="zone-box">
                        <div class="zone-label">Seat / Zone</div>
                        <div class="zone-value">Zone A</div>
                        <div class="zone-sub">Row 3</div>
                    </div>
                </td>
            </tr>
        </table>

        <div class="footer">
            From <strong>{{ $ticket->event->city }}</strong> to <strong>{{ $ticket->event->venue }}</strong>
        </div>

        <div class="bottom-band"></div>
    </div>
</div>

<style>
@page { margin: 18mm 12mm; }
body { margin: 0; font-family: DejaVu Sans, Arial, sans-serif; background: #ffffff; color: #e5edf9; }
.page { width: 100%; }
.ticket { width: 100%; background: #111827; border: 1px solid #d8e1ef; border-radius: 16px; overflow: hidden; page-break-inside: avoid; }
.top-band { height: 6px; background: linear-gradient(90deg, #2563eb 0%, #7c3aed 50%, #dc2626 100%); }
.bottom-band { height: 4px; background: linear-gradient(90deg, #2563eb 0%, #7c3aed 50%, #dc2626 100%); }
.ticket-cancelled .top-band, .ticket-cancelled .bottom-band { background: linear-gradient(90deg, #dc2626 0%, #991b1b 100%); }
.hero { width: 100%; background: #172665; border-collapse: collapse; }
.hero-copy { width: 68%; padding: 22px 24px 18px; vertical-align: top; }
.hero-image { width: 32%; vertical-align: top; background: #21316f; }
.hero-image img { display: block; width: 100%; height: 220px; object-fit: cover; }
.image-fallback { height: 220px; text-align: center; line-height: 220px; font-size: 18px; font-weight: 700; color: rgba(255,255,255,.75); }
.status-pill { display: inline-block; padding: 6px 14px; border-radius: 999px; font-size: 12px; font-weight: 700; margin-bottom: 16px; border: 1px solid; }
.status-pill.success { background: rgba(34,197,94,.12); border-color: rgba(34,197,94,.35); color: #2dd47a; }
.status-pill.muted { background: rgba(255,255,255,.08); border-color: rgba(255,255,255,.18); color: rgba(255,255,255,.78); }
.status-pill.danger { background: rgba(220,38,38,.16); border-color: rgba(248,113,113,.35); color: #fca5a5; }
h1 { margin: 0 0 10px; font-size: 26px; line-height: 1.15; color: #ffffff; }
.desc { margin: 0 0 18px; font-size: 13px; line-height: 1.6; color: rgba(255,255,255,.78); }
.meta { border-collapse: separate; border-spacing: 0 8px; width: auto; }
.meta td { background: rgba(255,255,255,.08); border: 1px solid rgba(255,255,255,.14); border-radius: 8px; padding: 7px 12px; font-size: 12px; font-weight: 700; color: rgba(255,255,255,.86); }
.strip { background: #0f172a; padding: 11px 24px; border-top: 1px solid rgba(255,255,255,.06); border-bottom: 1px solid rgba(255,255,255,.06); }
.code { color: #60a5fa; font-size: 17px; font-family: Courier, monospace; font-weight: 700; }
.type { float: right; color: rgba(255,255,255,.62); font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; }
.details { width: 100%; border-collapse: collapse; background: #111827; }
.details td { width: 25%; padding: 16px 24px 6px; vertical-align: top; }
.label { display: block; margin-bottom: 6px; font-size: 11px; color: rgba(255,255,255,.42); text-transform: uppercase; letter-spacing: .08em; font-weight: 700; }
.details strong { display: block; font-size: 14px; color: #f3f6fb; }
.mono { color: #60a5fa !important; font-family: Courier, monospace; }
.qr-row { width: 100%; border-collapse: collapse; background: #111827; border-top: 1px solid rgba(255,255,255,.06); }
.qr-row td { padding: 18px 24px 22px; vertical-align: middle; }
.qr-cell { width: 150px; }
.scan-copy { width: auto; }
.zone-cell { width: 190px; }
.qr-box { width: 120px; height: 120px; padding: 10px; background: #ffffff; border-radius: 12px; }
.qr-box img { width: 100%; height: 100%; display: block; }
.qr-placeholder { text-align: center; line-height: 100px; color: #111827; font-weight: 700; }
.scan-title { margin-bottom: 8px; font-size: 20px; font-weight: 700; color: #f3f6fb; }
.scan-sub { font-size: 13px; line-height: 1.6; color: rgba(255,255,255,.7); }
.zone-box { padding: 14px 16px; text-align: center; border: 1px solid rgba(59,130,246,.35); border-radius: 12px; background: rgba(37,99,235,.12); }
.zone-label { font-size: 11px; text-transform: uppercase; letter-spacing: .08em; color: rgba(255,255,255,.45); font-weight: 700; }
.zone-value { margin-top: 7px; font-size: 24px; color: #60a5fa; font-weight: 700; }
.zone-sub { margin-top: 4px; font-size: 12px; color: rgba(255,255,255,.5); }
.footer { background: #0d1321; padding: 14px 24px 18px; text-align: left; font-size: 13px; color: rgba(255,255,255,.55); border-top: 1px solid rgba(255,255,255,.06); }
.footer strong { color: rgba(255,255,255,.88); }
</style>
</body>
</html>
