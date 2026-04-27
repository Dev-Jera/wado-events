<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
@php
/* ═══════════════════════════════════════════════════
   THEME REGISTRY
   Each theme defines every color used in the ticket.
   stripeType: solid | kente | duo
   dark: true = light text on dark body background
   ═══════════════════════════════════════════════════ */
$tmpl = $batch->template ?? 'classic';

$themes = [

    /* ── Classic ─────────────────────────────────── */
    'classic' => [
        'accent'     => '#2563eb',
        'bodyBg'     => '#ffffff',
        'stubBg'     => '#eff6ff',
        'border'     => '#2563eb',
        'dash'       => '#93c5fd',
        'brand'      => '#2563eb',
        'event'      => '#1e293b',
        'meta'       => '#64748b',
        'metaBold'   => '#1e293b',
        'price'      => '#2563eb',
        'freeColor'  => '#16a34a',
        'code'       => '#1e293b',
        'labelBg'    => '#2563eb',
        'labelFg'    => '#ffffff',
        'dark'       => false,
        'stripeType' => 'solid',
    ],

    /* ── VIP Gold ────────────────────────────────── */
    'vip_gold' => [
        'accent'     => '#f59e0b',
        'bodyBg'     => '#0f172a',
        'stubBg'     => '#1e293b',
        'border'     => '#f59e0b',
        'dash'       => '#334155',
        'brand'      => '#f59e0b',
        'event'      => '#f8fafc',
        'meta'       => '#94a3b8',
        'metaBold'   => '#e2e8f0',
        'price'      => '#f59e0b',
        'freeColor'  => '#4ade80',
        'code'       => '#cbd5e1',
        'labelBg'    => '#f59e0b',
        'labelFg'    => '#0f172a',
        'dark'       => true,
        'stripeType' => 'solid',
    ],

    /* ── Cultural ────────────────────────────────── */
    'cultural' => [
        'accent'     => '#c2510f',
        'bodyBg'     => '#fdf8f0',
        'stubBg'     => '#fff7ed',
        'border'     => '#c2510f',
        'dash'       => '#fed7aa',
        'brand'      => '#c2510f',
        'event'      => '#1c1917',
        'meta'       => '#57534e',
        'metaBold'   => '#292524',
        'price'      => '#c2510f',
        'freeColor'  => '#16a34a',
        'code'       => '#44403c',
        'labelBg'    => '#c2510f',
        'labelFg'    => '#ffffff',
        'dark'       => false,
        'stripeType' => 'kente',
    ],

    /* ── Festival ────────────────────────────────── */
    'festival' => [
        'accent'     => '#7c3aed',
        'bodyBg'     => '#ffffff',
        'stubBg'     => '#faf5ff',
        'border'     => '#7c3aed',
        'dash'       => '#c4b5fd',
        'brand'      => '#7c3aed',
        'event'      => '#1e1b4b',
        'meta'       => '#6b7280',
        'metaBold'   => '#1e1b4b',
        'price'      => '#7c3aed',
        'freeColor'  => '#16a34a',
        'code'       => '#4c1d95',
        'labelBg'    => '#ec4899',
        'labelFg'    => '#ffffff',
        'dark'       => false,
        'stripeType' => 'duo',
    ],

    /* ── Corporate ───────────────────────────────── */
    'corporate' => [
        'accent'     => '#0f172a',
        'bodyBg'     => '#f8fafc',
        'stubBg'     => '#f1f5f9',
        'border'     => '#334155',
        'dash'       => '#cbd5e1',
        'brand'      => '#0891b2',
        'event'      => '#0f172a',
        'meta'       => '#64748b',
        'metaBold'   => '#0f172a',
        'price'      => '#0891b2',
        'freeColor'  => '#16a34a',
        'code'       => '#334155',
        'labelBg'    => '#0f172a',
        'labelFg'    => '#ffffff',
        'dark'       => false,
        'stripeType' => 'solid',
    ],
];

$t = $themes[$tmpl] ?? $themes['classic'];

/* ── Layout ────────────────────────────────────────── */
$perRow      = match($batch->ticket_size) { 'large' => 1, 'standard' => 2, default => 4 };
$rowsPerPage = match($batch->ticket_size) { 'large' => 3, 'standard' => 4, default => 5 };
$chunks      = $tickets->chunk($perRow);
$rowCount    = 0;
$maxTitle    = match($batch->ticket_size) { 'large' => 60, 'standard' => 45, default => 28 };
$maxVenue    = match($batch->ticket_size) { 'large' => 50, 'standard' => 38, default => 22 };
@endphp
<style>
@page {
    margin: 8mm;
}

* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body {
    font-family: 'DejaVu Sans', Arial, sans-serif;
    background: #f0f0f0;
    color: #1a1a1a;
}

.page-break {
    page-break-after: always;
}

/* ── Row of tickets ─────────────────────────────── */
.ticket-row {
    display: table;
    width: 100%;
    margin-bottom: {{ $gapMm }}mm;
    border-spacing: 0;
}

.ticket-cell {
    display: table-cell;
    vertical-align: top;
    padding-right: 3mm;
    width: {{ $cellWidthPct }}%;
}

.ticket-cell:last-child {
    padding-right: 0;
}

/* ── Single ticket shell ─────────────────────────── */
.ticket {
    width: 100%;
    height: {{ $ticketHeightMm }}mm;
    border: 1.5px solid {{ $t['border'] }};
    border-radius: 3mm;
    overflow: hidden;
    display: table;
    table-layout: fixed;
    background: {{ $t['bodyBg'] }};
}

/* ── Body (left) ─────────────────────────────────── */
.ticket-body {
    display: table-cell;
    vertical-align: top;
    width: {{ $bodyWidthPct }}%;
    padding: 0;
    background: {{ $t['bodyBg'] }};
    border-right: 2px dashed {{ $t['dash'] }};
}

.ticket-content {
    padding: {{ $padMm * 0.6 }}mm {{ $padMm }}mm {{ $padMm }}mm;
}

.ticket-brand {
    font-size: {{ $fontXsMm }}mm;
    font-weight: bold;
    letter-spacing: 0.08em;
    color: {{ $t['brand'] }};
    text-transform: uppercase;
    margin-bottom: 0.8mm;
}

.ticket-event {
    font-size: {{ $fontLgMm }}mm;
    font-weight: bold;
    color: {{ $t['event'] }};
    line-height: 1.25;
    margin-bottom: 0.8mm;
    overflow: hidden;
}

.ticket-label {
    font-size: {{ $fontSmMm }}mm;
    color: {{ $t['labelFg'] }};
    background: {{ $t['labelBg'] }};
    display: inline-block;
    padding: 0.4mm 1.5mm;
    border-radius: 1mm;
    margin-bottom: 1mm;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.ticket-meta {
    font-size: {{ $fontXsMm }}mm;
    color: {{ $t['meta'] }};
    line-height: 1.6;
}

.ticket-meta strong {
    color: {{ $t['metaBold'] }};
}

/* ── Stub (right) ────────────────────────────────── */
.ticket-stub {
    display: table-cell;
    vertical-align: middle;
    text-align: center;
    padding: {{ $padMm - 0.5 }}mm {{ $padMm }}mm;
    background: {{ $t['stubBg'] }};
    width: {{ 100 - $bodyWidthPct }}%;
}

.ticket-stub img {
    display: block;
    margin: 0 auto {{ $padMm * 0.5 }}mm;
    width: {{ $qrSizeMm }}mm;
    height: {{ $qrSizeMm }}mm;
}

.ticket-price {
    font-size: {{ $fontSmMm }}mm;
    font-weight: bold;
    color: {{ $t['price'] }};
    margin-bottom: 1mm;
}

.ticket-code {
    font-size: {{ $fontXsMm - 0.3 }}mm;
    font-family: 'DejaVu Sans Mono', monospace;
    color: {{ $t['code'] }};
    word-break: break-all;
    line-height: 1.35;
    text-align: center;
}

/* ── VIP label in stub ───────────────────────────── */
.vip-badge {
    font-size: {{ $fontXsMm - 0.2 }}mm;
    font-weight: bold;
    letter-spacing: 0.15em;
    color: {{ $t['accent'] }};
    text-transform: uppercase;
    margin-bottom: 1mm;
}

/* ── QR frame for VIP Gold ───────────────────────── */
.qr-frame {
    border: 1.5px solid {{ $t['accent'] }};
    border-radius: 2mm;
    display: inline-block;
    padding: 1mm;
    margin-bottom: {{ $padMm * 0.5 }}mm;
}

.qr-frame img {
    display: block;
    margin: 0;
    width: {{ $qrSizeMm - 2 }}mm;
    height: {{ $qrSizeMm - 2 }}mm;
}
</style>
</head>
<body>

@foreach ($chunks as $chunkIndex => $chunk)
<div class="ticket-row">
    @foreach ($chunk as $ticket)
    <div class="ticket-cell">
        <div class="ticket">

            {{-- ── Body ──────────────────────────────── --}}
            <div class="ticket-body">

                {{-- Top stripe --}}
                @if ($t['stripeType'] === 'kente')
                {{-- Kente-inspired multi-colour stripe --}}
                <table style="width:100%; height:{{ $stripeHeightMm }}mm; border-collapse:collapse; border-spacing:0;">
                    <tr>
                        <td style="background:#c2510f; padding:0;"></td>
                        <td style="background:#92400e; padding:0;"></td>
                        <td style="background:#fbbf24; padding:0;"></td>
                        <td style="background:#15803d; padding:0;"></td>
                        <td style="background:#c2510f; padding:0;"></td>
                        <td style="background:#92400e; padding:0;"></td>
                        <td style="background:#fbbf24; padding:0;"></td>
                        <td style="background:#0c4a6e; padding:0;"></td>
                        <td style="background:#c2510f; padding:0;"></td>
                    </tr>
                </table>
                @elseif ($t['stripeType'] === 'duo')
                {{-- Purple + pink split stripe --}}
                <table style="width:100%; height:{{ $stripeHeightMm }}mm; border-collapse:collapse; border-spacing:0;">
                    <tr>
                        <td style="background:#7c3aed; padding:0; width:62%;"></td>
                        <td style="background:#ec4899; padding:0; width:38%;"></td>
                    </tr>
                </table>
                @else
                {{-- Solid accent stripe (Classic, VIP Gold, Corporate) --}}
                <div style="height:{{ $stripeHeightMm }}mm; background:{{ $t['accent'] }};"></div>
                @endif

                {{-- Content --}}
                <div class="ticket-content">
                    <div class="ticket-brand">WADO Events</div>
                    <div class="ticket-event">{{ Str::limit($event->title, $maxTitle) }}</div>
                    <div class="ticket-label">{{ $batch->label }}</div>
                    <div class="ticket-meta">
                        <strong>Date:</strong> {{ $event->starts_at->format('d M Y') }}<br>
                        <strong>Time:</strong> {{ $event->starts_at->format('H:i') }}<br>
                        @if ($event->venue)
                        <strong>Venue:</strong> {{ Str::limit($event->venue, $maxVenue) }}
                        @endif
                    </div>
                </div>

            </div>{{-- /ticket-body --}}

            {{-- ── Stub ──────────────────────────────── --}}
            <div class="ticket-stub">

                @if ($tmpl === 'vip_gold')
                    <div class="vip-badge">VIP</div>
                @endif

                @if ($batch->price > 0)
                    <div class="ticket-price">UGX {{ number_format((float) $batch->price, 0) }}</div>
                @else
                    <div class="ticket-price" style="color:{{ $t['freeColor'] }};">FREE</div>
                @endif

                @if ($tmpl === 'vip_gold')
                    {{-- Gold-framed QR for VIP --}}
                    <div class="qr-frame">
                        <img src="{{ $ticket->qr_data_uri }}" alt="QR">
                    </div>
                @else
                    <img src="{{ $ticket->qr_data_uri }}" alt="QR">
                @endif

                <div class="ticket-code">{{ $ticket->ticket_code }}</div>

            </div>{{-- /ticket-stub --}}

        </div>{{-- /ticket --}}
    </div>{{-- /ticket-cell --}}
    @endforeach

    {{-- Pad remaining cells so the row stays aligned --}}
    @for ($e = $chunk->count(); $e < $perRow; $e++)
    <div class="ticket-cell"></div>
    @endfor
</div>{{-- /ticket-row --}}

@php
    $rowCount++;
    $isLastChunk = $chunkIndex === $chunks->count() - 1;
@endphp
@if (! $isLastChunk && $rowCount % $rowsPerPage === 0)
<div class="page-break"></div>
@php $rowCount = 0; @endphp
@endif

@endforeach

</body>
</html>
