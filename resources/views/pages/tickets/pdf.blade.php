<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket PDF</title>
</head>
<body>
@php
    $eventImage = $ticket->event->image_url;
    if ($eventImage && !str_starts_with($eventImage, 'http://') && !str_starts_with($eventImage, 'https://') && !str_starts_with($eventImage, '/')) {
        $eventImage = asset($eventImage);
    }
    // For PDF generation, only use external images or data URIs
    $useEventImage = $eventImage && (str_starts_with($eventImage, 'http://') || str_starts_with($eventImage, 'https://'));
    $isCancelled = $ticket->status === 'cancelled';
    $isUsed      = $ticket->status === 'used';
@endphp

<div class="pdf-ticket-container">
    <div class="pdf-ticket-card {{ $isCancelled ? 'pdf-card-cancelled' : '' }}">
        <div class="pdf-top-band {{ $isCancelled ? 'pdf-band-danger' : '' }}"></div>

        @if ($isCancelled)
        <div class="pdf-alert">
            <div class="pdf-alert-icon">⚠</div>
            This ticket was cancelled. Your refund has been processed.
        </div>
        @endif

        <div class="pdf-hero">
            <div class="pdf-hero-bg">
                <div class="pdf-bg-gradient"></div>
            </div>

            <div class="pdf-hero-left">
                <div class="pdf-status-pill {{ $isCancelled ? 'pdf-pill-danger' : ($isUsed ? 'pdf-pill-gray' : 'pdf-pill-green') }}">
                    <span class="pdf-status-dot"></span>
                    {{ $isCancelled ? 'Cancelled' : ($isUsed ? 'Used' : 'Confirmed') }}
                </div>
                <h1 class="pdf-title">{{ $ticket->event->title }}</h1>
                <p class="pdf-desc">{{ $ticket->event->description ? \Illuminate\Support\Str::limit($ticket->event->description, 130) : 'Plan your trip and keep your ticket details ready for check-in at the venue.' }}</p>
                <div class="pdf-meta-chips">
                    <div class="pdf-meta-chip">
                        <span class="pdf-meta-icon">📅</span>
                        {{ $ticket->event->starts_at->format('d M Y') }}
                    </div>
                    <div class="pdf-meta-chip">
                        <span class="pdf-meta-icon">🕐</span>
                        {{ $ticket->event->starts_at->format('H:i') }}
                    </div>
                    <div class="pdf-meta-chip">
                        <span class="pdf-meta-icon">📍</span>
                        {{ $ticket->event->city }}
                    </div>
                </div>
            </div>

            <div class="pdf-hero-right">
                @if ($useEventImage)
                    <img src="{{ $eventImage }}" alt="{{ $ticket->event->title }}" class="pdf-event-img">
                @else
                    <div class="pdf-img-placeholder">
                        <div class="pdf-placeholder-icon">🎫</div>
                        <span class="pdf-placeholder-label">{{ $ticket->ticketCategory->name }}</span>
                    </div>
                @endif
            </div>
        </div>

        <div class="pdf-strip">
            <span class="pdf-strip-code">{{ $ticket->ticket_code }}</span>
            <span class="pdf-strip-type">{{ $ticket->ticketCategory->name }} &middot; {{ $ticket->quantity }} ticket{{ $ticket->quantity > 1 ? 's' : '' }}</span>
        </div>

        <div class="pdf-tear">
            <div class="pdf-tear-circle"></div>
            <div class="pdf-tear-line"></div>
            <div class="pdf-tear-circle"></div>
        </div>

        <div class="pdf-details">
            <div class="pdf-details-grid">
                <div class="pdf-detail"><div class="pdf-dl">Ticket code</div><div class="pdf-dv pdf-mono">{{ $ticket->ticket_code }}</div></div>
                <div class="pdf-detail"><div class="pdf-dl">Category</div><div class="pdf-dv">{{ $ticket->ticketCategory->name }}</div></div>
                <div class="pdf-detail"><div class="pdf-dl">Quantity</div><div class="pdf-dv">{{ $ticket->quantity }} ticket{{ $ticket->quantity > 1 ? 's' : '' }}</div></div>
                <div class="pdf-detail"><div class="pdf-dl">Venue</div><div class="pdf-dv">{{ $ticket->event->venue }}</div></div>
                <div class="pdf-detail"><div class="pdf-dl">City</div><div class="pdf-dv">{{ $ticket->event->city }}</div></div>
                <div class="pdf-detail"><div class="pdf-dl">Purchased</div><div class="pdf-dv">{{ $ticket->purchased_at->format('d M Y') }}</div></div>
                <div class="pdf-detail"><div class="pdf-dl">Payment</div><div class="pdf-dv">{{ $ticket->payment_provider === 'free' ? 'Free' : strtoupper((string) $ticket->payment_provider) }}</div></div>
                <div class="pdf-detail">
                    <div class="pdf-dl">Status</div>
                    <div class="pdf-dv">
                        <span class="pdf-badge {{ $isCancelled ? 'pdf-badge-danger' : ($isUsed ? 'pdf-badge-gray' : 'pdf-badge-green') }}">
                            <span class="pdf-badge-dot"></span> {{ ucfirst($ticket->status) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="pdf-qr-row">
            <div class="pdf-qr-box">
                @if (!empty($qrCodeDataUri))
                    <img src="{{ $qrCodeDataUri }}" alt="QR Code" style="width:100%;height:100%;object-fit:contain;">
                @else
                    <div class="pdf-qr-placeholder">QR Code</div>
                @endif
            </div>
            <div class="pdf-qr-info">
                <div class="pdf-qr-title">Scan at entry</div>
                <div class="pdf-qr-sub">Present this QR code at the venue gate.<br>Screenshot or print for offline use.</div>
            </div>
            <div class="pdf-zone-box">
                <div class="pdf-zone-label">Seat / Zone</div>
                <div class="pdf-zone-val">Zone A</div>
                <div class="pdf-zone-sub">Row 3</div>
            </div>
        </div>

        <div class="pdf-footer">
            <div class="pdf-footer-route">
                <span class="pdf-route-dot"></span>
                From <strong>{{ $ticket->event->city }}</strong> &rarr; <strong>{{ $ticket->event->venue }}</strong>
            </div>
        </div>

        <div class="pdf-bottom-band {{ $isCancelled ? 'pdf-band-danger' : '' }}"></div>
    </div>
</div>

<style>
.pdf-ticket-container {
    font-family: 'Helvetica', 'Arial', sans-serif;
    max-width: 740px;
    margin: 0 auto;
    padding: 20px;
    background: white;
}

.pdf-ticket-card {
    border-radius: 20px;
    overflow: hidden;
    border: 1px solid #e5e7eb;
    box-shadow: 0 32px 64px rgba(8, 20, 48, 0.35);
}

.pdf-card-cancelled {
    border-color: rgba(220, 38, 38, 0.25);
}

.pdf-top-band {
    height: 5px;
    background: linear-gradient(90deg, #2563EB 0%, #7C3AED 50%, #DC2626 100%);
}

.pdf-bottom-band {
    height: 3px;
    background: linear-gradient(90deg, #2563EB, #7C3AED, #DC2626);
    opacity: 0.55;
}

.pdf-band-danger {
    background: linear-gradient(90deg, #DC2626, #991B1B) !important;
}

.pdf-alert {
    display: flex;
    align-items: center;
    gap: 10px;
    background: rgba(220, 38, 38, 0.12);
    border-bottom: 1px solid rgba(220, 38, 38, 0.2);
    color: #dc2626;
    font-size: 14px;
    font-weight: 600;
    padding: 10px 20px;
}

.pdf-alert-icon {
    font-size: 16px;
    flex-shrink: 0;
}

.pdf-hero {
    background: #0c1b5e;
    display: grid;
    grid-template-columns: 1fr 240px;
    min-height: 230px;
    position: relative;
    overflow: hidden;
}

.pdf-hero-bg {
    position: absolute;
    inset: 0;
    pointer-events: none;
}

.pdf-bg-gradient {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, rgba(37, 99, 235, 0.18) 0%, rgba(124, 58, 237, 0.13) 50%, rgba(220, 38, 38, 0.1) 100%);
}

.pdf-hero-left {
    padding: 26px 26px 22px;
    position: relative;
    z-index: 1;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.pdf-status-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    font-weight: 700;
    letter-spacing: 0.06em;
    padding: 4px 12px;
    border-radius: 999px;
    border: 1px solid;
    width: fit-content;
    margin-bottom: 12px;
}

.pdf-pill-green {
    background: rgba(34, 197, 94, 0.13);
    border-color: rgba(34, 197, 94, 0.3);
    color: #22c55e;
}

.pdf-pill-gray {
    background: rgba(255, 255, 255, 0.08);
    border-color: rgba(255, 255, 255, 0.18);
    color: rgba(255, 255, 255, 0.6);
}

.pdf-pill-danger {
    background: rgba(220, 38, 38, 0.18);
    border-color: rgba(220, 38, 38, 0.35);
    color: #f87171;
}

.pdf-status-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: currentColor;
    flex-shrink: 0;
}

.pdf-title {
    font-size: clamp(18px, 3vw, 24px);
    font-weight: 700;
    color: #fff;
    line-height: 1.15;
    letter-spacing: -0.4px;
    margin: 0 0 8px;
}

.pdf-desc {
    font-size: 14px;
    color: rgba(255, 255, 255, 0.48);
    line-height: 1.55;
    max-width: 300px;
}

.pdf-meta-chips {
    display: flex;
    gap: 7px;
    flex-wrap: wrap;
}

.pdf-meta-chip {
    display: flex;
    align-items: center;
    gap: 5px;
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.13);
    color: rgba(255, 255, 255, 0.72);
    font-size: 13px;
    font-weight: 600;
    padding: 5px 10px;
    border-radius: 8px;
}

.pdf-meta-icon {
    font-size: 12px;
}

.pdf-hero-right {
    position: relative;
    overflow: hidden;
}

.pdf-event-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.pdf-img-placeholder {
    width: 100%;
    height: 100%;
    min-height: 230px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 10px;
    background: rgba(37, 99, 235, 0.1);
}

.pdf-placeholder-icon {
    font-size: 52px;
}

.pdf-placeholder-label {
    font-size: 14px;
    font-weight: 600;
    color: rgba(255, 255, 255, 0.4);
}

.pdf-strip {
    background: #111827;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 24px;
    border-top: 1px solid rgba(255, 255, 255, 0.06);
    border-bottom: 1px solid rgba(255, 255, 255, 0.06);
}

.pdf-strip-code {
    font-family: 'Courier New', monospace;
    font-size: 14px;
    font-weight: 700;
    color: #60A5FA;
    letter-spacing: 0.08em;
}

.pdf-strip-type {
    font-size: 13px;
    font-weight: 700;
    color: rgba(255, 255, 255, 0.28);
    text-transform: uppercase;
    letter-spacing: 0.1em;
}

.pdf-tear {
    background: #111827;
    display: flex;
    align-items: center;
    padding: 0 14px;
}

.pdf-tear-circle {
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background: #0F172A;
    flex-shrink: 0;
    margin: 0 -11px;
    border: 1px solid rgba(255, 255, 255, 0.06);
}

.pdf-tear-line {
    flex: 1;
    border-top: 2px dashed rgba(255, 255, 255, 0.07);
    margin: 0 8px;
}

.pdf-details {
    background: #111827;
    padding: 22px 24px;
}

.pdf-details-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px 14px;
}

.pdf-dl {
    font-size: 12px;
    font-weight: 700;
    color: rgba(255, 255, 255, 0.28);
    text-transform: uppercase;
    letter-spacing: 0.1em;
    margin-bottom: 5px;
}

.pdf-dv {
    font-size: 14px;
    font-weight: 600;
    color: #E2E8F0;
}

.pdf-mono {
    font-family: 'Courier New', monospace;
    font-size: 13px;
    color: #60A5FA;
}

.pdf-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 13px;
    font-weight: 700;
    padding: 3px 10px;
    border-radius: 999px;
    border: 1px solid;
}

.pdf-badge-green {
    background: rgba(34, 197, 94, 0.1);
    border-color: rgba(34, 197, 94, 0.25);
    color: #22c55e;
}

.pdf-badge-gray {
    background: rgba(255, 255, 255, 0.06);
    border-color: rgba(255, 255, 255, 0.15);
    color: rgba(255, 255, 255, 0.5);
}

.pdf-badge-danger {
    background: rgba(220, 38, 38, 0.15);
    border-color: rgba(220, 38, 38, 0.3);
    color: #f87171;
}

.pdf-badge-dot {
    width: 5px;
    height: 5px;
    border-radius: 50%;
    background: currentColor;
}

.pdf-qr-row {
    background: #111827;
    padding: 16px 24px 20px;
    display: flex;
    align-items: center;
    gap: 18px;
    border-top: 1px solid rgba(255, 255, 255, 0.05);
}

.pdf-qr-box {
    width: 76px;
    height: 76px;
    background: #fff;
    border-radius: 10px;
    padding: 6px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}

.pdf-qr-box img {
    width: 100%;
    height: 100%;
}

.pdf-qr-placeholder {
    font-size: 10px;
    color: #666;
    text-align: center;
}

.pdf-qr-info {
    flex: 1;
}

.pdf-qr-title {
    font-size: 14px;
    font-weight: 700;
    color: #E2E8F0;
    margin-bottom: 4px;
}

.pdf-qr-sub {
    font-size: 12px;
    color: rgba(255, 255, 255, 0.32);
    line-height: 1.55;
}

.pdf-zone-box {
    background: rgba(37, 99, 235, 0.1);
    border: 1px solid rgba(37, 99, 235, 0.25);
    border-radius: 10px;
    padding: 10px 16px;
    text-align: center;
    flex-shrink: 0;
}

.pdf-zone-label {
    font-size: 12px;
    font-weight: 700;
    color: rgba(255, 255, 255, 0.3);
    text-transform: uppercase;
    letter-spacing: 0.08em;
    margin-bottom: 4px;
}

.pdf-zone-val {
    font-size: 16px;
    font-weight: 700;
    color: #60A5FA;
}

.pdf-zone-sub {
    font-size: 12px;
    color: rgba(255, 255, 255, 0.28);
    margin-top: 2px;
}

.pdf-footer {
    background: #0D1321;
    padding: 14px 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-top: 1px solid rgba(255, 255, 255, 0.06);
}

.pdf-footer-route {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    color: rgba(255, 255, 255, 0.38);
}

.pdf-footer-route strong {
    color: rgba(255, 255, 255, 0.72);
    font-weight: 700;
}

.pdf-route-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: #2563EB;
    flex-shrink: 0;
}

@media (max-width: 600px) {
    .pdf-hero {
        grid-template-columns: 1fr;
    }
    .pdf-hero-right {
        min-height: 160px;
    }
    .pdf-details-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    .pdf-ticket-container {
        padding: 10px;
    }
    .pdf-qr-row {
        flex-wrap: wrap;
    }
}
</style>
</body>
</html>
