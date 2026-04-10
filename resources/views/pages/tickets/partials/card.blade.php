@php
    $eventImage = $ticket->event->image_url;
    if ($eventImage && !str_starts_with($eventImage, 'http://') && !str_starts_with($eventImage, 'https://') && !str_starts_with($eventImage, '/')) {
        $eventImage = asset($eventImage);
    }
    $isCancelled = $ticket->status === 'cancelled';
    $isUsed = $ticket->status === 'used';
@endphp

<div class="tkd-card {{ $isCancelled ? 'tkd-card-cancelled' : '' }}">
    <div class="tkd-top-band {{ $isCancelled ? 'tkd-band-danger' : '' }}"></div>

    @if ($isCancelled)
    <div class="tkd-alert">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" style="flex-shrink:0;"><circle cx="12" cy="12" r="9" stroke="#f87171" stroke-width="1.5"/><path d="M12 8v4M12 16h.01" stroke="#f87171" stroke-width="1.5" stroke-linecap="round"/></svg>
        This ticket was cancelled. Your refund has been processed.
    </div>
    @endif

    <div class="tkd-hero">
        <div class="tkd-hero-bg" aria-hidden="true">
            <svg width="100%" height="100%" viewBox="0 0 720 240" preserveAspectRatio="xMidYMid slice">
                <circle cx="580" cy="-40" r="200" fill="#2563EB" opacity=".18"/>
                <circle cx="660" cy="260" r="160" fill="#7C3AED" opacity=".13"/>
                <circle cx="-20" cy="220" r="140" fill="#DC2626" opacity=".1"/>
                <line x1="0" y1="55" x2="460" y2="55" stroke="white" stroke-width=".4" opacity=".05"/>
                <line x1="0" y1="110" x2="460" y2="110" stroke="white" stroke-width=".4" opacity=".05"/>
                <line x1="0" y1="165" x2="460" y2="165" stroke="white" stroke-width=".4" opacity=".05"/>
                <line x1="115" y1="0" x2="115" y2="240" stroke="white" stroke-width=".4" opacity=".05"/>
                <line x1="230" y1="0" x2="230" y2="240" stroke="white" stroke-width=".4" opacity=".05"/>
                <line x1="345" y1="0" x2="345" y2="240" stroke="white" stroke-width=".4" opacity=".05"/>
            </svg>
        </div>

        <div class="tkd-hero-left">
            <div>
                <div class="tkd-status-pill {{ $isCancelled ? 'tkd-pill-danger' : ($isUsed ? 'tkd-pill-gray' : 'tkd-pill-green') }}">
                    <span class="tkd-status-dot"></span>
                    {{ $isCancelled ? 'Cancelled' : ($isUsed ? 'Used' : 'Confirmed') }}
                </div>
                <h1 class="tkd-title">{{ $ticket->event->title }}</h1>
                <p class="tkd-desc">{{ $ticket->event->description ? \Illuminate\Support\Str::limit($ticket->event->description, 130) : 'Plan your trip and keep your ticket details ready for check-in at the venue.' }}</p>
            </div>
            <div class="tkd-meta-chips">
                <div class="tkd-meta-chip">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none"><rect x="2" y="3" width="20" height="18" rx="3" stroke="currentColor" stroke-width="1.4"/><path d="M8 1v4M16 1v4M2 9h20" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/></svg>
                    {{ $ticket->event->starts_at->format('d M Y') }}
                </div>
                <div class="tkd-meta-chip">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.4"/><path d="M12 7v5l3 2" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/></svg>
                    {{ $ticket->event->starts_at->format('H:i') }}
                </div>
                <div class="tkd-meta-chip">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z" stroke="currentColor" stroke-width="1.4"/><circle cx="12" cy="9" r="2" stroke="currentColor" stroke-width="1.4"/></svg>
                    {{ $ticket->event->city }}
                </div>
            </div>
        </div>

        <div class="tkd-hero-right">
            @if ($eventImage)
                <img src="{{ $eventImage }}" alt="{{ $ticket->event->title }}" class="tkd-event-img">
            @else
                <div class="tkd-img-placeholder">
                    <svg class="tkd-placeholder-bg" width="100%" height="100%" viewBox="0 0 260 240" preserveAspectRatio="xMidYMid slice">
                        <rect width="260" height="240" fill="#0a1540"/>
                        <circle cx="200" cy="40" r="110" fill="#2563EB" opacity=".28"/>
                        <circle cx="40" cy="210" r="90" fill="#7C3AED" opacity=".2"/>
                        <circle cx="130" cy="120" r="55" fill="#DC2626" opacity=".1"/>
                    </svg>
                    <svg width="52" height="52" viewBox="0 0 24 24" fill="none" style="position:relative;z-index:1;">
                        <rect x="1" y="6" width="22" height="14" rx="3" stroke="#2563EB" stroke-width="1.3" fill="rgba(37,99,235,.12)"/>
                        <path d="M7 6V5a5 5 0 0110 0v1" stroke="#2563EB" stroke-width="1.3" opacity=".6"/>
                        <circle cx="12" cy="13" r="2.5" fill="#2563EB" opacity=".9"/>
                        <path d="M8 17h8" stroke="#2563EB" stroke-width="1.3" stroke-linecap="round" opacity=".4"/>
                    </svg>
                    <span class="tkd-placeholder-label">{{ $ticket->ticketCategory->name }}</span>
                </div>
            @endif
        </div>
    </div>

    <div class="tkd-strip">
        <span class="tkd-strip-code">{{ $ticket->ticket_code }}</span>
        <span class="tkd-strip-type">{{ $ticket->ticketCategory->name }} &middot; {{ $ticket->quantity }} ticket{{ $ticket->quantity > 1 ? 's' : '' }}</span>
    </div>

    <div class="tkd-tear">
        <div class="tkd-tear-circle"></div>
        <div class="tkd-tear-line"></div>
        <div class="tkd-tear-circle"></div>
    </div>

    <div class="tkd-details">
        <div class="tkd-details-grid">
            <div class="tkd-detail"><div class="tkd-dl">Ticket code</div><div class="tkd-dv tkd-mono">{{ $ticket->ticket_code }}</div></div>
            <div class="tkd-detail"><div class="tkd-dl">Category</div><div class="tkd-dv">{{ $ticket->ticketCategory->name }}</div></div>
            <div class="tkd-detail"><div class="tkd-dl">Quantity</div><div class="tkd-dv">{{ $ticket->quantity }} ticket{{ $ticket->quantity > 1 ? 's' : '' }}</div></div>
            <div class="tkd-detail"><div class="tkd-dl">Venue</div><div class="tkd-dv">{{ $ticket->event->venue }}</div></div>
            <div class="tkd-detail"><div class="tkd-dl">City</div><div class="tkd-dv">{{ $ticket->event->city }}</div></div>
            <div class="tkd-detail"><div class="tkd-dl">Purchased</div><div class="tkd-dv">{{ $ticket->purchased_at->format('d M Y') }}</div></div>
            <div class="tkd-detail"><div class="tkd-dl">Payment</div><div class="tkd-dv">{{ $ticket->payment_provider === 'free' ? 'Free' : strtoupper((string) $ticket->payment_provider) }}</div></div>
            <div class="tkd-detail">
                <div class="tkd-dl">Status</div>
                <div class="tkd-dv">
                    <span class="tkd-badge {{ $isCancelled ? 'tkd-badge-danger' : ($isUsed ? 'tkd-badge-gray' : 'tkd-badge-green') }}">
                        <span class="tkd-badge-dot"></span> {{ ucfirst($ticket->status) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="tkd-qr-row">
        <div class="tkd-qr-box">
            @if ($ticket->qr_code_url)
                <img src="{{ $ticket->qr_code_url }}" alt="QR Code" style="width:100%;height:100%;object-fit:contain;">
            @else
                <div class="tkd-qr-fallback">QR</div>
            @endif
        </div>
        <div class="tkd-qr-info">
            <div class="tkd-qr-title">Scan at entry</div>
            <div class="tkd-qr-sub">Present this QR code at the venue gate.<br>Screenshot or print for offline use.</div>
        </div>
        <div class="tkd-zone-box">
            <div class="tkd-zone-label">Seat / Zone</div>
            <div class="tkd-zone-val">Zone A</div>
            <div class="tkd-zone-sub">Row 3</div>
        </div>
    </div>

    <div class="tkd-footer">
        <div class="tkd-footer-route">
            <span class="tkd-route-dot"></span>
            From <strong>{{ $ticket->event->city }}</strong> &rarr; <strong>{{ $ticket->event->venue }}</strong>
        </div>
        <div class="tkd-footer-actions">
            <a href="{{ route('tickets.index') }}" class="tkd-btn tkd-btn-ghost">My Tickets</a>
            <a href="{{ route('tickets.download', $ticket) }}" class="tkd-btn tkd-btn-ghost" download>Download QR</a>
            <a href="{{ route('tickets.pdf', $ticket) }}" class="tkd-btn tkd-btn-primary" download>Download Ticket</a>
            <a href="{{ route('events.show', $ticket->event) }}" class="tkd-btn {{ $isCancelled ? 'tkd-btn-danger' : 'tkd-btn-ghost' }}">View Event</a>
        </div>
    </div>

    <div class="tkd-bottom-band {{ $isCancelled ? 'tkd-band-danger' : '' }}"></div>
</div>
