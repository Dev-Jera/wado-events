<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
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
    background: #fff;
    color: #1a1a1a;
}

/* ── Layout helpers ────────────────────────────── */

.page-break {
    page-break-after: always;
}

/* ── Ticket wrapper per row ─────────────────────── */

.ticket-row {
    display: table;
    width: 100%;
    margin-bottom: {{ $gapMm }}mm;
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

/* ── Single ticket shell ────────────────────────── */

.ticket {
    width: 100%;
    height: {{ $ticketHeightMm }}mm;
    border: 1.5px solid #c0283c;
    border-radius: 3mm;
    overflow: hidden;
    display: table;
    table-layout: fixed;
    background: #fff;
    box-shadow: 0 1px 3px rgba(0,0,0,.12);
}

/* ── Main body (left side) ──────────────────────── */

.ticket-body {
    display: table-cell;
    vertical-align: top;
    width: {{ $bodyWidthPct }}%;
    padding: {{ $padMm }}mm;
    background: #fff;
    border-right: 2px dashed #e8a0a8;
    position: relative;
}

/* top accent stripe */
.ticket-body::before {
    content: '';
    display: block;
    position: absolute;
    top: 0; left: 0; right: 0;
    height: {{ $stripeHeightMm }}mm;
    background: #c0283c;
}

.ticket-content {
    margin-top: {{ $stripeHeightMm + 1.5 }}mm;
}

.ticket-brand {
    font-size: {{ $fontXsMm }}mm;
    font-weight: bold;
    letter-spacing: 0.08em;
    color: #c0283c;
    text-transform: uppercase;
    margin-bottom: 0.8mm;
}

.ticket-event {
    font-size: {{ $fontLgMm }}mm;
    font-weight: bold;
    color: #1a1a1a;
    line-height: 1.25;
    margin-bottom: 0.6mm;
    overflow: hidden;
}

.ticket-label {
    font-size: {{ $fontSmMm }}mm;
    color: #fff;
    background: #c0283c;
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
    color: #555;
    line-height: 1.5;
}

.ticket-meta strong {
    color: #222;
}

/* ── Stub (right side — QR + code) ─────────────── */

.ticket-stub {
    display: table-cell;
    vertical-align: middle;
    text-align: center;
    padding: {{ $padMm - 0.5 }}mm {{ $padMm }}mm;
    background: #fdf0f2;
    width: {{ 100 - $bodyWidthPct }}%;
}

.ticket-stub img {
    display: block;
    margin: 0 auto {{ $padMm * 0.6 }}mm;
    width: {{ $qrSizeMm }}mm;
    height: {{ $qrSizeMm }}mm;
}

.ticket-code {
    font-size: {{ $fontXsMm - 0.3 }}mm;
    font-family: 'DejaVu Sans Mono', monospace;
    color: #1a1a1a;
    word-break: break-all;
    line-height: 1.3;
    text-align: center;
}

.ticket-price {
    font-size: {{ $fontSmMm }}mm;
    font-weight: bold;
    color: #c0283c;
    margin-bottom: 1mm;
}
</style>
</head>
<body>

@php
    $perRow    = $batch->ticket_size === 'small' ? 4 : ($batch->ticket_size === 'large' ? 1 : 2);
    $chunks    = $tickets->chunk($perRow);
    $rowsPerPage = $batch->ticket_size === 'small' ? 5 : ($batch->ticket_size === 'large' ? 3 : 4);
    $rowCount  = 0;
@endphp

@foreach ($chunks as $chunkIndex => $chunk)
<div class="ticket-row">
    @foreach ($chunk as $ticket)
    <div class="ticket-cell">
        <div class="ticket">

            {{-- Main body --}}
            <div class="ticket-body">
                <div class="ticket-content">
                    <div class="ticket-brand">WADO Events</div>
                    <div class="ticket-event">{{ Str::limit($event->title, $batch->ticket_size === 'small' ? 28 : 55) }}</div>
                    <div class="ticket-label">{{ $batch->label }}</div>
                    <div class="ticket-meta">
                        <strong>Date:</strong> {{ $event->starts_at->format('d M Y') }}<br>
                        <strong>Time:</strong> {{ $event->starts_at->format('H:i') }}<br>
                        @if($event->venue)
                        <strong>Venue:</strong> {{ Str::limit($event->venue, $batch->ticket_size === 'small' ? 22 : 40) }}
                        @endif
                    </div>
                </div>
            </div>

            {{-- Stub --}}
            <div class="ticket-stub">
                @if ($batch->price > 0)
                <div class="ticket-price">UGX {{ number_format((float)$batch->price, 0) }}</div>
                @else
                <div class="ticket-price" style="color:#16a34a;">FREE</div>
                @endif
                <img src="{{ $ticket->qr_data_uri }}" alt="QR">
                <div class="ticket-code">{{ $ticket->ticket_code }}</div>
            </div>

        </div>
    </div>
    @endforeach
    {{-- Fill remaining cells so layout stays aligned --}}
    @for ($e = $chunk->count(); $e < $perRow; $e++)
    <div class="ticket-cell"></div>
    @endfor
</div>

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
