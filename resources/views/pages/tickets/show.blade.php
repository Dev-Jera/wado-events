@extends('layouts.app')

@if (request()->query('modal') === '1')
    @section('fullbleed', '1')
@endif

@section('content')
@php
    $eventImage = $ticket->event->image_url;
    if ($eventImage && !str_starts_with($eventImage, 'http://') && !str_starts_with($eventImage, 'https://') && !str_starts_with($eventImage, '/')) {
        $eventImage = asset($eventImage);
    }
    $isCancelled = $ticket->status === \App\Models\Ticket::STATUS_CANCELLED;
    $isUsed      = $ticket->status === \App\Models\Ticket::STATUS_USED;
    $isModal     = request()->query('modal') === '1';
@endphp

@if (! $isModal)
<a href="{{ route('tickets.index') }}" class="tkd-back">
    <svg width="13" height="13" viewBox="0 0 24 24" fill="none">
        <path d="M19 12H5M12 5l-7 7 7 7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
    Back to my tickets
</a>
@endif

<div class="tkd-wrap {{ $isModal ? 'tkd-modal-mode' : '' }}">
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
                <line x1="0" y1="55"  x2="460" y2="55"  stroke="white" stroke-width=".4" opacity=".05"/>
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
                        <circle cx="200" cy="40"  r="110" fill="#2563EB" opacity=".28"/>
                        <circle cx="40"  cy="210" r="90"  fill="#7C3AED" opacity=".2"/>
                        <circle cx="130" cy="120" r="55"  fill="#DC2626" opacity=".1"/>
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
                <svg viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="21" height="21" fill="white"/>
                    <rect x="1" y="1" width="7" height="7" rx="1" fill="#0F172A"/><rect x="2" y="2" width="5" height="5" rx=".5" fill="white"/><rect x="3" y="3" width="3" height="3" fill="#0F172A"/>
                    <rect x="13" y="1" width="7" height="7" rx="1" fill="#0F172A"/><rect x="14" y="2" width="5" height="5" rx=".5" fill="white"/><rect x="15" y="3" width="3" height="3" fill="#0F172A"/>
                    <rect x="1" y="13" width="7" height="7" rx="1" fill="#0F172A"/><rect x="2" y="14" width="5" height="5" rx=".5" fill="white"/><rect x="3" y="15" width="3" height="3" fill="#0F172A"/>
                    <rect x="9" y="1" width="2" height="2" fill="#0F172A"/><rect x="9" y="4" width="2" height="2" fill="#0F172A"/><rect x="9" y="7" width="4" height="2" fill="#0F172A"/>
                    <rect x="13" y="9" width="2" height="3" fill="#0F172A"/><rect x="16" y="9" width="2" height="2" fill="#0F172A"/>
                    <rect x="9" y="11" width="2" height="2" fill="#0F172A"/><rect x="9" y="14" width="3" height="2" fill="#0F172A"/>
                    <rect x="13" y="13" width="2" height="2" fill="#0F172A"/><rect x="16" y="12" width="3" height="2" fill="#0F172A"/>
                    <rect x="9" y="17" width="5" height="2" fill="#0F172A"/><rect x="15" y="16" width="2" height="3" fill="#0F172A"/>
                    <rect x="18" y="15" width="2" height="4" fill="#0F172A"/><rect x="11" y="9" width="2" height="2" fill="#0F172A"/>
                </svg>
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
            <a href="{{ route('tickets.download', $ticket) }}" class="tkd-btn tkd-btn-ghost">Download QR</a>
            <a href="{{ route('tickets.pdf', $ticket) }}" class="tkd-btn tkd-btn-primary">Download Ticket</a>
            <a href="{{ route('events.show', $ticket->event) }}" class="tkd-btn {{ $isCancelled ? 'tkd-btn-danger' : 'tkd-btn-ghost' }}">View Event</a>
        </div>
    </div>

    <div class="tkd-bottom-band {{ $isCancelled ? 'tkd-band-danger' : '' }}"></div>

</div>
</div>

<style>
.tkd-back{display:inline-flex;align-items:center;gap:6px;font-size:.78rem;font-weight:600;color:#dbe7ff;text-decoration:none;font-family:var(--site-font);margin:1.2rem 0 .8rem max(1rem,calc((100% - 740px)/2));transition:color .15s;}
.tkd-back:hover{color:#1d67d6;}
.tkd-wrap{font-family:var(--site-font);max-width:740px;margin:0 auto 3rem;padding:0 1rem;}
.tkd-wrap button,.tkd-wrap input,.tkd-wrap select,.tkd-wrap textarea{font-family:inherit;}
.tkd-modal-mode{padding:0;margin:0 auto;max-width:100%;}
.tkd-card{border-radius:20px;overflow:hidden;border:1px solid rgba(255,255,255,.06);box-shadow:0 32px 64px rgba(8,20,48,.35);}
.tkd-card-cancelled{border-color:rgba(220,38,38,.25);}
.tkd-top-band{height:5px;background:linear-gradient(90deg,#2563EB 0%,#7C3AED 50%,#DC2626 100%);}
.tkd-bottom-band{height:3px;background:linear-gradient(90deg,#2563EB,#7C3AED,#DC2626);opacity:.55;}
.tkd-band-danger{background:linear-gradient(90deg,#DC2626,#991B1B) !important;}
.tkd-alert{display:flex;align-items:center;gap:10px;background:rgba(220,38,38,.12);border-bottom:1px solid rgba(220,38,38,.2);color:#f87171;font-size:.76rem;font-weight:600;padding:10px 20px;}
.tkd-hero{background:#0c1b5e;display:grid;grid-template-columns:1fr 240px;min-height:230px;position:relative;overflow:hidden;}
.tkd-hero-bg{position:absolute;inset:0;pointer-events:none;}
.tkd-hero-left{padding:26px 26px 22px;position:relative;z-index:1;display:flex;flex-direction:column;justify-content:space-between;}
.tkd-status-pill{display:inline-flex;align-items:center;gap:6px;font-size:.65rem;font-weight:700;letter-spacing:.06em;padding:4px 12px;border-radius:999px;border:1px solid;width:fit-content;margin-bottom:12px;}
.tkd-pill-green{background:rgba(34,197,94,.13);border-color:rgba(34,197,94,.3);color:#4ade80;}
.tkd-pill-gray{background:rgba(255,255,255,.08);border-color:rgba(255,255,255,.18);color:rgba(255,255,255,.6);}
.tkd-pill-danger{background:rgba(220,38,38,.18);border-color:rgba(220,38,38,.35);color:#f87171;}
.tkd-status-dot{width:6px;height:6px;border-radius:50%;background:currentColor;flex-shrink:0;}
.tkd-title{font-size:clamp(18px,3vw,24px);font-weight:700;color:#fff;line-height:1.15;letter-spacing:-.4px;margin:0 0 8px;}
.tkd-desc{font-size:.76rem;color:rgba(255,255,255,.48);line-height:1.55;max-width:300px;}
.tkd-meta-chips{display:flex;gap:7px;flex-wrap:wrap;}
.tkd-meta-chip{display:flex;align-items:center;gap:5px;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.13);color:rgba(255,255,255,.72);font-size:.68rem;font-weight:600;padding:5px 10px;border-radius:8px;}
.tkd-hero-right{position:relative;overflow:hidden;}
.tkd-event-img{width:100%;height:100%;object-fit:cover;display:block;}
.tkd-img-placeholder{width:100%;height:100%;min-height:230px;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:10px;position:relative;}
.tkd-placeholder-bg{position:absolute;inset:0;}
.tkd-placeholder-label{position:relative;z-index:1;font-size:.72rem;font-weight:600;color:rgba(255,255,255,.4);}
.tkd-strip{background:#111827;display:flex;align-items:center;justify-content:space-between;padding:10px 24px;border-top:1px solid rgba(255,255,255,.06);border-bottom:1px solid rgba(255,255,255,.06);}
.tkd-strip-code{font-family:'Courier New',monospace;font-size:.82rem;font-weight:700;color:#60A5FA;letter-spacing:.08em;}
.tkd-strip-type{font-size:.65rem;font-weight:700;color:rgba(255,255,255,.28);text-transform:uppercase;letter-spacing:.1em;}
.tkd-tear{background:#111827;display:flex;align-items:center;padding:0 14px;}
.tkd-tear-circle{width:22px;height:22px;border-radius:50%;background:#0F172A;flex-shrink:0;margin:0 -11px;border:1px solid rgba(255,255,255,.06);}
.tkd-tear-line{flex:1;border-top:2px dashed rgba(255,255,255,.07);margin:0 8px;}
.tkd-details{background:#111827;padding:22px 24px;}
.tkd-details-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:20px 14px;}
.tkd-dl{font-size:.58rem;font-weight:700;color:rgba(255,255,255,.28);text-transform:uppercase;letter-spacing:.1em;margin-bottom:5px;}
.tkd-dv{font-size:.82rem;font-weight:600;color:#E2E8F0;}
.tkd-mono{font-family:'Courier New',monospace;font-size:.75rem;color:#60A5FA;}
.tkd-badge{display:inline-flex;align-items:center;gap:5px;font-size:.65rem;font-weight:700;padding:3px 10px;border-radius:999px;border:1px solid;}
.tkd-badge-green{background:rgba(34,197,94,.1);border-color:rgba(34,197,94,.25);color:#4ade80;}
.tkd-badge-gray{background:rgba(255,255,255,.06);border-color:rgba(255,255,255,.15);color:rgba(255,255,255,.5);}
.tkd-badge-danger{background:rgba(220,38,38,.15);border-color:rgba(220,38,38,.3);color:#f87171;}
.tkd-badge-dot{width:5px;height:5px;border-radius:50%;background:currentColor;}
.tkd-qr-row{background:#111827;padding:16px 24px 20px;display:flex;align-items:center;gap:18px;border-top:1px solid rgba(255,255,255,.05);}
.tkd-qr-box{width:128px;height:128px;background:#fff;border-radius:12px;padding:10px;flex-shrink:0;display:flex;align-items:center;justify-content:center;}
.tkd-qr-box svg,.tkd-qr-box img{width:100%;height:100%;object-fit:contain;}
.tkd-qr-info{flex:1;}
.tkd-qr-title{font-size:.78rem;font-weight:700;color:#E2E8F0;margin-bottom:4px;}
.tkd-qr-sub{font-size:.68rem;color:rgba(255,255,255,.32);line-height:1.55;}
.tkd-zone-box{background:rgba(37,99,235,.1);border:1px solid rgba(37,99,235,.25);border-radius:10px;padding:10px 16px;text-align:center;flex-shrink:0;}
.tkd-zone-label{font-size:.58rem;font-weight:700;color:rgba(255,255,255,.3);text-transform:uppercase;letter-spacing:.08em;margin-bottom:4px;}
.tkd-zone-val{font-size:1rem;font-weight:700;color:#60A5FA;}
.tkd-zone-sub{font-size:.62rem;color:rgba(255,255,255,.28);margin-top:2px;}
.tkd-footer{background:#0D1321;padding:14px 24px;display:flex;align-items:center;justify-content:space-between;border-top:1px solid rgba(255,255,255,.06);flex-wrap:wrap;gap:10px;}
.tkd-footer-route{display:flex;align-items:center;gap:8px;font-size:.72rem;color:rgba(255,255,255,.38);}
.tkd-footer-route strong{color:rgba(255,255,255,.72);font-weight:700;}
.tkd-route-dot{width:7px;height:7px;border-radius:50%;background:#2563EB;flex-shrink:0;}
.tkd-footer-actions{display:flex;gap:8px;}
.tkd-btn{display:inline-block;text-decoration:none;border-radius:9px;padding:8px 16px;font-size:.72rem;font-weight:700;font-family:inherit;cursor:pointer;border:none;transition:all .15s;}
.tkd-btn-primary{background:#2563EB;color:#fff;}
.tkd-btn-primary:hover{background:#1D4ED8;color:#fff;}
.tkd-btn-ghost{background:rgba(255,255,255,.06);color:rgba(255,255,255,.55);border:1px solid rgba(255,255,255,.12);}
.tkd-btn-ghost:hover{background:rgba(255,255,255,.1);color:#fff;}
.tkd-btn-danger{background:#DC2626;color:#fff;}
.tkd-btn-danger:hover{background:#B91C1C;color:#fff;}
@media(max-width:600px){
    .tkd-hero{grid-template-columns:1fr;}
    .tkd-hero-right{min-height:160px;}
    .tkd-details-grid{grid-template-columns:repeat(2,1fr);}
    .tkd-footer{flex-direction:column;align-items:flex-start;}
    .tkd-wrap{padding:0 .5rem;}
    .tkd-qr-row{flex-wrap:wrap;}
}

@media print {
    body { background: #fff !important; }
    body * { visibility: hidden !important; }
    .tkd-wrap, .tkd-wrap * { visibility: visible !important; }
    .tkd-wrap { max-width: 100% !important; margin: 0 !important; padding: 0 !important; }
    .tkd-footer-actions, .tkd-back, .tkd-social, .tkd-footer-route { display: none !important; }
    .tkd-card { box-shadow: none !important; border: none !important; }
}
</style>
@endsection
