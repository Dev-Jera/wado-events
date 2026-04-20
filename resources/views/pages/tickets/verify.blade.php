@extends('layouts.app')

@if (request()->boolean('embedded'))
    @section('fullbleed', '1')
@endif

@if (request()->boolean('scanner_only'))
    @section('fullbleed', '1')
@endif

@section('content')
    @php
        $verification = session('verification');
        $lookupResults = collect(session('lookup_results', []));
        $payload = is_array($verification['payload'] ?? null) ? $verification['payload'] : null;
        $events = collect($events ?? []);
        $verificationRows = collect($verificationRows ?? []);
        $selectedEventId = (int) old('selected_event_id', $selectedEventId ?? 0);
        $scannerOnly = request()->boolean('scanner_only');
        $isEmbedded = request()->boolean('embedded');
        $returnToScannerUrl = request('back') ?: url('/dashboard/scanner-page');
        $initialScannerFeedback = $verification['message']
            ?? ($selectedEventId > 0 ? 'Event selected. Start camera to scan.' : 'Choose an event first, then start the camera.');
        $initialScannerTone = $verification
            ? (($verification['ok'] ?? false) ? 'success' : 'error')
            : ($selectedEventId > 0 ? 'neutral' : 'warning');
    @endphp

    <section class="vp {{ $scannerOnly ? 'vp-scanner-only' : '' }}">
        <div class="vp-shell">

            {{-- ── PAGE HEADER ── --}}
            @if(!request()->boolean('embedded') && ! $scannerOnly)
            <header class="vp-header">
                <div class="vp-header-badge">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2z"/><path d="M13 5v2"/><path d="M13 17v2"/><path d="M13 11v2"/></svg>
                </div>
                <div>
                    <h1>Ticket Verification</h1>
                    <p>Scan QR codes, verify by code, or use offline export when internet is unavailable.</p>
                </div>
            </header>
            @endif

            {{-- ── EVENT SELECTOR BAR (always at top, above camera) ── --}}
            @unless($scannerOnly)
            <div class="vp-event-bar">
                <label class="vp-event-bar-label" for="selected-event-id">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2z"/></svg>
                    Gate Event
                </label>
                <select id="selected-event-id" name="selected_event_id" class="vp-input vp-event-bar-select" required>
                    <option value="">Select event…</option>
                    @foreach ($events as $event)
                        <option value="{{ $event->id }}" @selected($selectedEventId === (int) $event->id)>
                            {{ $event->title }} ({{ $event->starts_at?->format('d M, H:i') }})
                        </option>
                    @endforeach
                </select>
            </div>
            @endunless

            <div class="vp-grid {{ $scannerOnly ? 'vp-grid-scanner-only' : '' }}">

                {{-- ── LEFT COLUMN ── --}}
                <div class="vp-left">

                    {{-- Scanner card --}}
                    <div class="vp-card scanner-card {{ $scannerOnly ? 'scanner-card-only' : '' }}">
                        <div class="vp-card-head">
                            <div class="vp-card-title">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7V5a2 2 0 0 1 2-2h2"/><path d="M17 3h2a2 2 0 0 1 2 2v2"/><path d="M21 17v2a2 2 0 0 1-2 2h-2"/><path d="M7 21H5a2 2 0 0 1-2-2v-2"/><rect width="7" height="7" x="7" y="7" rx="1"/></svg>
                                Camera Scanner
                            </div>
                            <span id="scanner-status" class="vp-badge badge-idle">Idle</span>
                        </div>

                        <div id="scanner-feedback" class="scanner-feedback fb-{{ $initialScannerTone }}">
                            {{ $initialScannerFeedback }}
                        </div>

                        <div id="qr-reader" class="qr-viewport"></div>

                        <div class="scanner-actions">
                            <button type="button" id="start-scan" class="btn btn-primary">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="6 3 20 12 6 21 6 3"/></svg>
                                Start camera
                            </button>
                            <button type="button" id="stop-scan" class="btn btn-ghost" disabled>
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect width="6" height="16" x="4" y="4" rx="1"/><rect width="6" height="16" x="14" y="4" rx="1"/></svg>
                                Stop camera
                            </button>
                            @if($scannerOnly)
                                <a href="{{ $returnToScannerUrl }}" class="btn btn-outline">← Back</a>
                            @endif
                        </div>
                    </div>

                    {{-- Offline tools --}}
                    @unless($scannerOnly)
                    <div class="vp-card">
                        <div class="vp-card-head">
                            <div class="vp-card-title">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.99 12a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.92 1.16h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9a16 16 0 0 0 6.93 6.93l1.17-1.17a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/><path d="M16.5 1.5a5 5 0 0 1 5 5"/><path d="M16.5 5.5a1 1 0 0 1 1 1"/></svg>
                                Offline Tools
                            </div>
                            @if ($selectedEventId > 0)
                                <a href="{{ route('tickets.verify.export', ['event_id' => $selectedEventId]) }}" class="vp-link">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
                                    Export tickets
                                </a>
                            @else
                                <span class="vp-muted">Select an event to export</span>
                            @endif
                        </div>

                        <div class="offline-row">
                            <label class="file-label">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>
                                Load JSON export
                                <input type="file" id="offline-file" accept="application/json">
                            </label>
                            <input type="text" id="offline-search" class="vp-input" placeholder="Search by code, name, or phone…">
                        </div>
                        <div id="offline-table-wrap"></div>
                    </div>
                    @endunless

                </div>

                {{-- ── RIGHT COLUMN ── --}}
                <div class="vp-right {{ $scannerOnly ? 'vp-right-hidden' : '' }}">

                    {{-- Mobile "other methods" toggle --}}
                    <button type="button" class="vp-fallback-toggle" id="vp-fallback-toggle" aria-expanded="false">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                        Can't scan? Other methods
                        <svg class="vp-toggle-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                    </button>

                    <div class="vp-fallback-body" id="vp-fallback-body">

                    {{-- Verify form --}}
                    <div class="vp-card">
                        <div class="vp-card-head">
                            <div class="vp-card-title">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                Verify Ticket
                            </div>
                        </div>

                        <form method="POST" action="{{ route('tickets.verify.store') }}" class="vp-form" id="verify-form">
                            @csrf
                            <input type="hidden" id="scanned-payload" name="scanned_payload" value="{{ old('scanned_payload') }}">
                            <input type="hidden" id="device-id" name="device_id" value="{{ old('device_id') }}">
                            {{-- event id synced from the top bar select via JS --}}
                            <input type="hidden" id="verify-form-event-id" name="selected_event_id" value="{{ $selectedEventId ?: '' }}">

                            <div class="field">
                                <label class="field-label" for="ticket-code-input">Ticket Code</label>
                                <div class="input-with-icon">
                                    <svg class="input-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                    <input id="ticket-code-input" type="text" name="ticket_code" class="vp-input has-icon" value="{{ old('ticket_code') }}" placeholder="WADO-1-7-ABC123" required>
                                </div>
                            </div>

                            <button id="verify-submit" type="submit" class="btn btn-primary btn-full">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                Verify Ticket
                            </button>
                        </form>
                    </div>

                    {{-- Lookup form --}}
                    <div class="vp-card">
                        <div class="vp-card-head">
                            <div class="vp-card-title">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                                Attendee Lookup
                            </div>
                        </div>
                        <form method="POST" action="{{ route('tickets.verify.store') }}" class="vp-form">
                            @csrf
                            <input type="hidden" class="js-sync-event-id" name="selected_event_id" value="{{ $selectedEventId ?: '' }}">
                            <div class="field">
                                <label class="field-label">Search by name, phone, or email</label>
                                <div class="input-with-icon">
                                    <svg class="input-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                    <input type="text" name="lookup" class="vp-input has-icon" value="{{ old('lookup') }}" placeholder="e.g. John Smith / +41…">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-outline btn-full">Search Attendee</button>
                        </form>
                    </div>

                    {{-- Verification result --}}
                    @if ($verification)
                        <div class="vp-result {{ $verification['ok'] ? 'result-ok' : 'result-bad' }}">
                            <div class="result-head">
                                @if ($verification['ok'])
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                    Valid Ticket
                                @else
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" x2="9" y1="9" y2="15"/><line x1="9" x2="15" y1="9" y2="15"/></svg>
                                    Invalid Ticket
                                @endif
                            </div>
                            <p class="result-msg">{{ $verification['message'] }}</p>

                            @if (! empty($verification['ticket']))
                                <div class="result-grid">
                                    <div class="result-item"><span>Ticket code</span>{{ $verification['ticket']->ticket_code }}</div>
                                    <div class="result-item"><span>Event</span>{{ $verification['ticket']->event->title }}</div>
                                    <div class="result-item"><span>Holder</span>{{ $verification['ticket']->holder_name ?: $verification['ticket']->user->name }}</div>
                                    <div class="result-item"><span>Phone</span>{{ $verification['ticket']->user->phone ?: 'N/A' }}</div>
                                    @if ($verification['ticket']->payer_name)
                                        <div class="result-item"><span>Payer</span>{{ $verification['ticket']->payer_name }}</div>
                                    @endif
                                    <div class="result-item"><span>Status</span>{{ ucfirst((string) $verification['ticket']->status) }}</div>
                                </div>
                            @endif
                        </div>
                    @endif

                    </div>{{-- /vp-fallback-body --}}
                </div>
            </div>

            {{-- QR payload + lookup results (full-width) --}}
            @unless($scannerOnly)
            @if ($payload)
                <div class="vp-card vp-card-full">
                    <div class="vp-card-head">
                        <div class="vp-card-title">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7V5a2 2 0 0 1 2-2h2"/><path d="M17 3h2a2 2 0 0 1 2 2v2"/><path d="M21 17v2a2 2 0 0 1-2 2h-2"/><path d="M7 21H5a2 2 0 0 1-2-2v-2"/></svg>
                            Scanned QR Payload
                        </div>
                        <span class="vp-muted">read-only</span>
                    </div>
                    <div class="table-wrap">
                        <table class="vp-table">
                            <tbody>
                                @foreach ($payload as $key => $value)
                                    <tr>
                                        <th>{{ $key }}</th>
                                        <td>{{ is_scalar($value) ? (string) $value : json_encode($value) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            @if ($lookupResults->isNotEmpty())
                <div class="vp-card vp-card-full">
                    <div class="vp-card-head">
                        <div class="vp-card-title">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                            Name / Phone Matches
                        </div>
                        <span class="vp-badge badge-blue">{{ $lookupResults->count() }} found</span>
                    </div>
                    <div class="table-wrap">
                        <table class="vp-table">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Event</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($lookupResults as $ticket)
                                    <tr>
                                        <td class="mono">{{ $ticket->ticket_code }}</td>
                                        <td>{{ $ticket->holder_name ?: $ticket->user->name }}</td>
                                        <td>{{ $ticket->user->phone ?: 'N/A' }}</td>
                                        <td>{{ $ticket->event->title }}</td>
                                        <td>
                                            <span class="status-pill {{ strtolower((string) $ticket->status) === \App\Models\Ticket::STATUS_USED ? 'pill-used' : 'pill-valid' }}">
                                                {{ ucfirst((string) $ticket->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <div class="vp-card vp-card-full">
                <div class="vp-card-head">
                    <div class="vp-card-title">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3h18v18H3z"/><path d="M3 9h18"/><path d="M9 21V9"/></svg>
                        Verification Audit Table
                    </div>
                    <span class="vp-muted">Names, QR payload, signature checks, scan state</span>
                </div>

                @if ($selectedEventId <= 0)
                    <p class="vp-muted" style="margin:0;">Select a Gate Event above to load verification audit rows.</p>
                @elseif ($verificationRows->isEmpty())
                    <p class="vp-muted" style="margin:0;">No tickets found for this event.</p>
                @else
                    <div class="table-wrap">
                        <table class="vp-table">
                            <thead>
                                <tr>
                                    <th>Holder</th>
                                    <th>Ticket Code</th>
                                    <th>Payment</th>
                                    <th>Generated QR Payload</th>
                                    <th>Generated Signature</th>
                                    <th>Scan State</th>
                                    <th>Last Scan Result</th>
                                    <th>Last Scanned Signature</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($verificationRows as $row)
                                    @php
                                        $ticket = $row['ticket'];
                                        $paymentStatus = strtoupper((string) ($ticket->paymentTransaction?->status ?? 'N/A'));
                                        $scanState = $ticket->used_at || $ticket->status === \App\Models\Ticket::STATUS_USED
                                            ? 'Scanned/used'
                                            : 'Waiting for scan';
                                        $scanStatusClass = $scanState === 'Scanned/used' ? 'pill-used' : 'pill-pending';
                                        $lastScan = $row['latest_scan'];
                                        $lastResult = strtoupper((string) ($lastScan?->result ?? 'NOT_SCANNED'));
                                        $resultClass = $lastResult === 'VALID'
                                            ? 'pill-valid'
                                            : ($lastResult === 'NOT_SCANNED' ? 'pill-pending' : 'pill-bad');
                                        $generatedSigOk = (bool) ($row['generated_signature_ok'] ?? false);
                                        $lastSigOk = $row['latest_signature_ok'];
                                    @endphp
                                    <tr>
                                        <td>{{ $ticket->holder_name ?: ($ticket->user?->name ?? 'N/A') }}</td>
                                        <td class="mono">{{ $ticket->ticket_code }}</td>
                                        <td>
                                            <span class="status-pill {{ $paymentStatus === \App\Models\PaymentTransaction::STATUS_CONFIRMED ? 'pill-valid' : 'pill-bad' }}">{{ $paymentStatus }}</span>
                                        </td>
                                        <td class="payload-cell">
                                            <details>
                                                <summary>View payload</summary>
                                                <pre>{{ $row['generated_payload_json'] }}</pre>
                                            </details>
                                        </td>
                                        <td>
                                            <span class="status-pill {{ $generatedSigOk ? 'pill-valid' : 'pill-bad' }}">{{ $generatedSigOk ? 'TRUE' : 'FALSE' }}</span>
                                        </td>
                                        <td>
                                            <span class="status-pill {{ $scanStatusClass }}">{{ $scanState }}</span>
                                        </td>
                                        <td>
                                            <span class="status-pill {{ $resultClass }}">{{ $lastResult }}</span>
                                            @if ($lastScan?->scanned_at)
                                                <div class="vp-muted" style="margin-top:4px;">{{ $lastScan->scanned_at->format('d M H:i:s') }}</div>
                                            @endif
                                        </td>
                                        <td>
                                            @if (is_bool($lastSigOk))
                                                <span class="status-pill {{ $lastSigOk ? 'pill-valid' : 'pill-bad' }}">{{ $lastSigOk ? 'TRUE' : 'FALSE' }}</span>
                                            @else
                                                <span class="status-pill pill-pending">N/A</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
            @endunless

        </div>
    </section>

    {{-- ── FULLSCREEN SCAN RESULT OVERLAY (scanner_only mode) ── --}}
    <div id="scan-overlay" class="sco sco-hidden" role="alert" aria-live="assertive">
        <div class="sco-body">
            <div class="sco-icon-wrap" id="sco-icon-wrap">
                {{-- filled by JS --}}
            </div>
            <p class="sco-holder" id="sco-holder"></p>
            <p class="sco-event"  id="sco-event"></p>
            <p class="sco-msg"    id="sco-msg"></p>
            <p class="sco-code"   id="sco-code"></p>
        </div>
        <div class="sco-progress"><div class="sco-progress-fill" id="sco-progress-fill"></div></div>
    </div>

    <style>

        /* ── tokens ── */
        :root {
            --wado-admin-font: 'Quicksand', 'Nunito', 'Plus Jakarta Sans', 'Segoe UI', sans-serif;
            --blue:      #0A4FBE;
            --blue-dark: #083F98;
            --blue-light:#E8F0FF;
            --blue-mid:  #C9D9F8;
            --blue-tint: #F2F7FF;
            --red:       #0A4FBE;
            --red-dark:  #083F98;
            --red-light: #E8F0FF;
            --red-mid:   #C9D9F8;
            --white:     #FFFFFF;
            --off:       #EDF1FA;
            --border:    #D4DCF0;
            --text:      #111827;
            --muted:     #5A6A8A;
            --radius:    13px;
            --shadow:    0 2px 10px rgba(26,79,191,.1), 0 1px 3px rgba(0,0,0,.06);
        }

        /* ── layout ── */
        .vp {
            min-height:100vh;
            background:var(--off);
            padding:7rem 1rem 3rem;
            font-family: var(--wado-admin-font);
        }

        .vp,
        .vp *:not(svg):not(path):not(rect):not(circle):not(line):not(polyline):not(polygon),
        .vp button,
        .vp input,
        .vp select,
        .vp textarea,
        .vp table,
        .vp th,
        .vp td,
        .vp label,
        .vp span,
        .vp strong,
        .vp small,
        .vp a {
            font-family: var(--wado-admin-font);
        }

        @if (request()->boolean('embedded'))
        .vp { padding: 1rem; }
        @endif
        .vp-shell { width:min(1100px, 100%); margin:0 auto; display:flex; flex-direction:column; gap:1.25rem; }
        .vp-grid-scanner-only { grid-template-columns: 1fr; }
        .scanner-card-only { max-width: 980px; margin: 0 auto; }
        .vp-right-hidden { display: none !important; }

        .vp-scanner-only {
            min-height: 100dvh;
            padding: 0;
            background: #000;
        }

        .vp-scanner-only .vp-shell {
            width: 100%;
            min-height: 100dvh;
            margin: 0;
            max-width: none;
            gap: 0;
        }

        .vp-scanner-only .vp-grid,
        .vp-scanner-only .vp-left {
            display: block;
            min-height: 100dvh;
            height: 100dvh;
        }

        .vp-scanner-only .scanner-card {
            position: relative;
            border: 0;
            border-radius: 0;
            margin: 0;
            padding: 0;
            box-shadow: none;
            min-height: 100dvh;
            height: 100dvh;
            background: #000;
        }

        .vp-scanner-only .vp-card-head {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            z-index: 6;
            margin: 0;
            border-radius: 0;
            padding: .65rem .8rem;
            border-bottom: 0;
            background: linear-gradient(180deg, rgba(0,0,0,.7), rgba(0,0,0,0));
        }

        .vp-scanner-only .vp-card-title,
        .vp-scanner-only .vp-badge {
            color: #fff;
            border-color: rgba(255,255,255,.35);
            background: rgba(0,0,0,.35);
        }

        .vp-scanner-only .scanner-feedback {
            position: absolute;
            top: 3.1rem;
            left: .8rem;
            right: .8rem;
            z-index: 6;
            margin: 0;
            background: rgba(0,0,0,.55);
            color: #fff;
            border-color: rgba(255,255,255,.3);
            backdrop-filter: blur(2px);
        }

        .vp-scanner-only .qr-viewport {
            width: 100%;
            min-height: 100dvh;
            height: 100dvh;
            border: 0;
            border-radius: 0;
            background: #000;
        }

        .vp-scanner-only .scanner-actions {
            position: absolute;
            bottom: 1rem;
            left: .75rem;
            right: .75rem;
            z-index: 6;
            margin: 0;
            background: rgba(0,0,0,.55);
            border: 1px solid rgba(255,255,255,.2);
            border-radius: 12px;
            padding: .5rem;
            display: flex;
            gap: .4rem;
            flex-wrap: nowrap;
        }
        .vp-scanner-only .scanner-actions .btn {
            flex: 1;
            justify-content: center;
            min-width: 0;
            font-size: .78rem;
            padding: 0 .5rem;
            white-space: nowrap;
        }

        /* ── header ── */
        .vp-header {
            display:flex; align-items:center; gap:1rem;
            background:linear-gradient(135deg,var(--blue) 0%,var(--blue-dark) 100%);
            border-radius:var(--radius); padding:1.4rem 1.6rem; color:var(--white);
        }
        .vp-header-badge {
            flex-shrink:0; width:44px; height:44px; border-radius:10px;
            background:rgba(255,255,255,.18); display:flex; align-items:center; justify-content:center;
        }
        .vp-header h1 { margin:0; font-size:1.25rem; font-weight:700; letter-spacing:0; }
        .vp-header p  { margin:.2rem 0 0; font-size:.82rem; opacity:.8; }
        /* ── 2-col grid ── */
        .vp-grid { display:grid; grid-template-columns:1fr 1fr; gap:1.25rem; }
        .vp-left, .vp-right { display:flex; flex-direction:column; gap:1.25rem; }

        /* ── card ── */
        .vp-card {
            background:var(--white); border:1px solid var(--border);
            border-radius:var(--radius); padding:1.15rem 1.3rem;
            box-shadow:var(--shadow);
        }
        .vp-card-full { width:100%; }
        .vp-card-head {
            display:flex; align-items:center; justify-content:space-between;
            gap:.6rem;
            background:var(--blue-tint); margin:-1.15rem -1.3rem 1rem;
            padding:.75rem 1.3rem; border-radius:var(--radius) var(--radius) 0 0;
            border-bottom:1px solid var(--border);
        }
        .vp-card-title {
            display:flex; align-items:center; gap:.45rem;
            font-size:.82rem; font-weight:600; color:var(--blue);
            text-transform:none; letter-spacing:.01em;
        }

        /* ── scanner ── */
        .scanner-card { border-top:3px solid var(--red); }
        .scanner-feedback {
            border-radius:8px; padding:.65rem .8rem;
            font-size:.83rem; font-weight:600; border:1px solid;
            margin-bottom:.85rem;
        }
        .fb-neutral  { background:var(--blue-tint); color:var(--blue);    border-color:var(--blue-mid); }
        .fb-info     { background:var(--blue-light); color:#0e4fa8;        border-color:var(--blue-mid); }
        .fb-success  { background:#F0FBF4;            color:#146c3a;        border-color:#B8E8CA; }
        .fb-warning  { background:#FFFAED;            color:#8B5C00;        border-color:#F5DFA0; }
        .fb-error    { background:var(--red-light);  color:var(--red-dark);border-color:var(--red-mid); }
        .qr-viewport {
            width:100%; min-height:220px;
            border:2px dashed var(--blue-mid); border-radius:10px;
            background:var(--blue-tint); overflow:hidden;
        }
        .scanner-actions { margin-top:.75rem; display:flex; gap:.5rem; }

        /* ── buttons ── */
        .btn {
            display:inline-flex; align-items:center; gap:.45rem;
            height:40px; padding:0 1rem; border:none; border-radius:8px;
            font-size:.84rem; font-weight:600; cursor:pointer; transition:.15s;
        }
        .btn-primary  { background:var(--red); color:var(--white); }
        .btn-primary:hover { background:var(--red-dark); }
        .btn-ghost    { background:var(--blue-tint); color:var(--blue); border:1px solid var(--border); }
        .btn-ghost:hover { background:var(--blue-mid); }
        .btn-outline  { background:var(--blue-tint); color:var(--blue); border:1.5px solid var(--blue); }
        .btn-outline:hover { background:var(--blue-mid); }
        .btn-full     { width:100%; justify-content:center; }
        .btn[disabled] { opacity:.4; cursor:not-allowed; }

        /* ── forms ── */
        .vp-form { display:flex; flex-direction:column; gap:.85rem; }
        .field { display:flex; flex-direction:column; gap:.35rem; }
        .field-label { font-size:.74rem; font-weight:600; color:var(--muted); text-transform:none; letter-spacing:.01em; }
        .vp-input {
            height:42px; border:1.5px solid var(--border); border-radius:8px;
            padding:0 .85rem; font-size:.875rem; color:var(--text);
            background:var(--blue-tint);
            transition:border-color .15s, background .15s;
        }
        .vp-input:focus { outline:none; border-color:var(--blue); background:var(--white); }
        .input-with-icon { position:relative; }
        .input-icon { position:absolute; left:.75rem; top:50%; transform:translateY(-50%); color:var(--muted); pointer-events:none; }
        .vp-input.has-icon { padding-left:2.4rem; }

        /* ── offline ── */
        .offline-row { display:grid; grid-template-columns:1fr 1fr; gap:.65rem; margin-bottom:.75rem; }
        .file-label {
            display:flex; align-items:center; gap:.5rem; height:42px;
            border:1.5px solid var(--border); border-radius:8px; padding:0 .85rem;
            font-size:.83rem; font-weight:600; color:var(--blue); cursor:pointer;
            background:var(--blue-tint); white-space:nowrap; overflow:hidden;
        }
        .file-label:hover { background:var(--blue-mid); }
        .file-label input[type=file] { display:none; }

        /* ── tables ── */
        .table-wrap { overflow-x:auto; border-radius:8px; border:1px solid var(--border); }
        .vp-table { width:100%; border-collapse:collapse; font-size:.83rem; }
        .vp-table thead th {
            background:var(--blue); color:var(--white); padding:.5rem .75rem;
            text-align:left; font-size:.74rem; font-weight:600; text-transform:none; letter-spacing:.01em;
        }
        .vp-table tbody td, .vp-table tbody th {
            padding:.55rem .75rem; border-bottom:1px solid var(--border); color:var(--text); vertical-align:middle;
        }
        .vp-table tbody th { background:var(--blue-tint); font-weight:600; color:var(--muted); width:30%; }
        .vp-table tbody tr:last-child td, .vp-table tbody tr:last-child th { border-bottom:none; }
        .vp-table tbody tr:hover td { background:var(--blue-tint); }
        .mono { font-family:monospace; font-size:.8rem; color:var(--blue); font-weight:700; }
        .payload-cell details { max-width: 330px; }
        .payload-cell summary { cursor: pointer; color: var(--blue); font-weight: 600; }
        .payload-cell pre {
            margin-top: .35rem;
            background: #0f172a;
            color: #dbeafe;
            border-radius: 6px;
            padding: .45rem .55rem;
            font-size: .72rem;
            overflow: auto;
        }
        .pill-pending { background:#EEF3FF; color:#1A4FBF; border-color:#D0DCFA; }
        .pill-bad { background:#FFF0F0; color:#B01E1E; border-color:#FCDCDC; }

        /* ── result ── */
        .vp-result { border-radius:10px; padding:1rem 1.1rem; border:1.5px solid; }
        .result-ok  { background:var(--blue-light); border-color:var(--blue-mid); color:var(--blue-dark); }
        .result-bad { background:var(--red-light);  border-color:var(--red-mid);  color:var(--red-dark); }
        .result-head { display:flex; align-items:center; gap:.5rem; font-weight:700; font-size:1rem; }
        .result-msg  { margin:.45rem 0 0; font-size:.875rem; opacity:.85; }
        .result-grid { margin:.75rem 0 0; display:grid; grid-template-columns:1fr 1fr; gap:.4rem .75rem; }
        .result-item { font-size:.83rem; color:var(--text); }
        .result-item span { display:block; font-size:.72rem; font-weight:600; text-transform:none; letter-spacing:.01em; opacity:.65; }

        /* ── badges ── */
        .vp-badge { display:inline-flex; align-items:center; height:22px; border-radius:100px; padding:0 .6rem; font-size:.72rem; font-weight:600; text-transform:none; letter-spacing:.01em; }
        .badge-idle { background:var(--off); color:var(--muted); border:1px solid var(--border); }
        .badge-scan { background:var(--red-light); color:var(--red); border:1px solid var(--red-mid); }
        .badge-blue { background:var(--blue-light); color:var(--blue); border:1px solid var(--blue-mid); }
        .vp-link    { display:inline-flex; align-items:center; gap:.35rem; color:var(--red); font-size:.82rem; font-weight:700; text-decoration:none; }
        .vp-link:hover { text-decoration:underline; }
        .vp-muted   { font-size:.8rem; color:var(--muted); }

        /* status pills */
        .status-pill { display:inline-block; padding:.15rem .55rem; border-radius:100px; font-size:.75rem; font-weight:700; }
        .pill-valid { background:var(--blue-light); color:var(--blue); border:1px solid var(--blue-mid); }
        .pill-used  { background:var(--red-light);  color:var(--red); border:1px solid var(--red-mid); }

        /* ── event selector bar (above grid) ── */
        .vp-event-bar {
            display: flex;
            align-items: center;
            gap: .75rem;
            background: #fff;
            border: 1.5px solid var(--border);
            border-radius: var(--radius);
            padding: .75rem 1.1rem;
            box-shadow: var(--shadow);
        }
        .vp-event-bar-label {
            display: flex;
            align-items: center;
            gap: .4rem;
            font-size: .78rem;
            font-weight: 700;
            color: var(--blue);
            white-space: nowrap;
            flex-shrink: 0;
        }
        .vp-event-bar-select { flex: 1; margin: 0; }

        /* ── fallback toggle (mobile only) ── */
        .vp-fallback-toggle {
            display: none;
        }
        .vp-fallback-body {
            display: contents; /* always visible on desktop */
        }

        /* ── responsive ── */
        @media (max-width: 820px) {
            .vp-grid { grid-template-columns: 1fr; }
            .vp-event-bar { flex-direction: column; align-items: stretch; }
            .vp-event-bar-label { justify-content: flex-start; }
            .offline-row { grid-template-columns: 1fr; }
            .result-grid { grid-template-columns: 1fr; }

            /* Show the toggle button, hide the body by default */
            .vp-fallback-toggle {
                display: flex;
                align-items: center;
                gap: .55rem;
                width: 100%;
                background: #fff;
                border: 1.5px solid var(--border);
                border-radius: var(--radius);
                padding: .8rem 1.1rem;
                font-size: .85rem;
                font-weight: 700;
                color: var(--blue);
                cursor: pointer;
                box-shadow: var(--shadow);
            }
            .vp-toggle-chevron {
                margin-left: auto;
                transition: transform .2s;
            }
            .vp-fallback-toggle[aria-expanded="true"] .vp-toggle-chevron {
                transform: rotate(180deg);
            }
            .vp-fallback-body {
                display: none;
                flex-direction: column;
                gap: 1.25rem;
            }
            .vp-fallback-body.open {
                display: flex;
            }
            /* auto-open if there's a verification result or error */
            .vp-fallback-body.has-result {
                display: flex;
            }
        }

        /* ── Fullscreen scan result overlay ── */
        .sco {
            position: fixed; inset: 0; z-index: 9999;
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            padding: 2rem 1.5rem 0;
            transition: opacity .18s ease;
        }
        .sco-hidden { opacity: 0; pointer-events: none; }
        .sco-visible { opacity: 1; pointer-events: auto; }

        .sco-ok   { background: #16a34a; }
        .sco-bad  { background: #c0283c; }
        .sco-warn { background: #b45309; }

        .sco-body {
            flex: 1; display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            gap: .75rem; text-align: center;
        }

        .sco-icon-wrap {
            width: 90px; height: 90px; border-radius: 50%;
            background: rgba(255,255,255,.2);
            display: flex; align-items: center; justify-content: center;
            margin-bottom: .5rem;
        }
        .sco-icon-wrap svg { width: 52px; height: 52px; stroke: #fff; }

        .sco-holder {
            margin: 0; font-size: clamp(1.6rem, 8vw, 2.6rem);
            font-weight: 800; color: #fff; line-height: 1.1;
            letter-spacing: -.02em;
        }
        .sco-event {
            margin: 0; font-size: clamp(.9rem, 4vw, 1.15rem);
            color: rgba(255,255,255,.82); font-weight: 600; line-height: 1.3;
        }
        .sco-msg {
            margin: .25rem 0 0; font-size: clamp(.85rem, 3.5vw, 1rem);
            color: rgba(255,255,255,.7); font-weight: 500;
        }
        .sco-code {
            margin: 0; font-family: monospace;
            font-size: clamp(.7rem, 3vw, .88rem);
            color: rgba(255,255,255,.5); letter-spacing: .05em;
        }

        /* countdown progress bar at very bottom */
        .sco-progress {
            width: 100%; height: 5px;
            background: rgba(255,255,255,.2);
            position: absolute; bottom: 0; left: 0;
        }
        .sco-progress-fill {
            height: 100%; background: rgba(255,255,255,.7);
            width: 100%;
            transition: width linear;
        }
    </style>

    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script>
    (function () {
        const scannerOnly      = @json($scannerOnly);
        const isEmbedded       = @json($isEmbedded);
        const scanJsonUrl      = @json(route('tickets.verify.scan-json'));
        const csrfToken        = document.querySelector('meta[name=csrf-token]')?.content
                                 || document.cookie.match(/XSRF-TOKEN=([^;]+)/)?.[1]?.replace(/%3D/g,'=') || '';
        const fullScannerBaseUrl = @json(route('tickets.verify.index', ['scanner_only' => 1, 'back' => $returnToScannerUrl]));

        const readerTargetId = 'qr-reader';
        const statusEl       = document.getElementById('scanner-status');
        const feedbackEl     = document.getElementById('scanner-feedback');
        const startBtn       = document.getElementById('start-scan');
        const stopBtn        = document.getElementById('stop-scan');
        const eventSelect    = document.getElementById('selected-event-id');
        const codeInput      = document.getElementById('ticket-code-input');
        const payloadInput   = document.getElementById('scanned-payload');
        const deviceInput    = document.getElementById('device-id');
        const submitBtn      = document.getElementById('verify-submit');
        const offlineFile    = document.getElementById('offline-file');
        const offlineSearch  = document.getElementById('offline-search');
        const offlineTableWrap = document.getElementById('offline-table-wrap');

        // overlay elements
        const overlay        = document.getElementById('scan-overlay');
        const scoIconWrap    = document.getElementById('sco-icon-wrap');
        const scoHolder      = document.getElementById('sco-holder');
        const scoEvent       = document.getElementById('sco-event');
        const scoMsg         = document.getElementById('sco-msg');
        const scoCode        = document.getElementById('sco-code');
        const scoProgressFill= document.getElementById('sco-progress-fill');

        if (!startBtn || !stopBtn || !codeInput || !statusEl || !feedbackEl || !eventSelect || typeof Html5Qrcode === 'undefined') return;

        let scanner = null, running = false, lastCode = '', scanLocked = false,
            scanWatchdog = null, selectedCameraLabel = '', overlayTimer = null;

        // ── Device ID ──────────────────────────────────────────────────
        const getDeviceId = () => {
            const key = 'ticket_verify_device_id';
            let v = localStorage.getItem(key);
            if (!v) { v = 'scanner-' + Math.random().toString(36).slice(2, 10); localStorage.setItem(key, v); }
            return v;
        };
        if (deviceInput) deviceInput.value = getDeviceId();

        // ── Audio feedback ─────────────────────────────────────────────
        const playSound = (ok) => {
            try {
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                if (ok) {
                    [[880, 0, .12], [1320, .13, .15]].forEach(([freq, when, dur]) => {
                        const o = ctx.createOscillator(), g = ctx.createGain();
                        o.connect(g); g.connect(ctx.destination);
                        o.type = 'sine'; o.frequency.value = freq;
                        g.gain.setValueAtTime(.28, ctx.currentTime + when);
                        g.gain.exponentialRampToValueAtTime(.001, ctx.currentTime + when + dur);
                        o.start(ctx.currentTime + when);
                        o.stop(ctx.currentTime + when + dur + .05);
                    });
                } else {
                    const o = ctx.createOscillator(), g = ctx.createGain();
                    o.connect(g); g.connect(ctx.destination);
                    o.type = 'sawtooth';
                    o.frequency.setValueAtTime(220, ctx.currentTime);
                    o.frequency.exponentialRampToValueAtTime(80, ctx.currentTime + .35);
                    g.gain.setValueAtTime(.35, ctx.currentTime);
                    g.gain.exponentialRampToValueAtTime(.001, ctx.currentTime + .35);
                    o.start(ctx.currentTime); o.stop(ctx.currentTime + .4);
                }
            } catch (_) {}
        };

        // ── Fullscreen result overlay (scanner_only mode) ──────────────
        const OVERLAY_DURATION = 3000;

        // Colour by reason: valid=green, already_used=amber, everything else=red
        const overlayColor = (result) => {
            if (result.ok)                         return 'sco-ok';
            if (result.reason === 'already_used')  return 'sco-warn';
            if (result.reason === 'wrong_event')   return 'sco-warn';
            return 'sco-bad';
        };

        const showOverlay = (result) => {
            if (!overlay) return;
            clearTimeout(overlayTimer);

            overlay.className = 'sco sco-visible ' + overlayColor(result);

            scoIconWrap.innerHTML = result.ok
                ? `<svg viewBox="0 0 24 24" fill="none" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>`
                : (result.reason === 'already_used' || result.reason === 'wrong_event'
                    ? `<svg viewBox="0 0 24 24" fill="none" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>`
                    : `<svg viewBox="0 0 24 24" fill="none" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>`
                );

            scoHolder.textContent = result.holder || (result.ok ? 'Welcome in!' : 'Access Denied');
            scoEvent.textContent  = result.ok
                ? [result.event, result.category].filter(Boolean).join(' · ')
                : result.message || '';
            scoMsg.textContent    = result.detail || '';
            scoCode.textContent   = result.code ? ('CODE: ' + result.code) : '';

            if (scoProgressFill) {
                scoProgressFill.style.transition = 'none';
                scoProgressFill.style.width = '100%';
                requestAnimationFrame(() => requestAnimationFrame(() => {
                    scoProgressFill.style.transition = `width ${OVERLAY_DURATION}ms linear`;
                    scoProgressFill.style.width = '0%';
                }));
            }

            playSound(result.ok);
            overlayTimer = setTimeout(hideOverlay, OVERLAY_DURATION);
        };

        const hideOverlay = () => {
            clearTimeout(overlayTimer);
            if (overlay) overlay.className = 'sco sco-hidden';
            scanLocked = false;
            lastCode   = '';   // allow re-scanning same code after dismiss
        };

        if (overlay) overlay.addEventListener('click', hideOverlay);

        // ── Helpers ────────────────────────────────────────────────────
        const setStatus   = (text) => { statusEl.textContent = text; };
        const setFeedback = (text, tone = 'neutral') => {
            feedbackEl.textContent = text;
            feedbackEl.className = 'scanner-feedback fb-' + tone;
        };
        const hasSelectedEvent = () => String(eventSelect.value || '').trim() !== '';
        const syncScannerButtons = () => {
            if (running) { startBtn.disabled = true; stopBtn.disabled = false; return; }
            startBtn.disabled = !hasSelectedEvent();
            stopBtn.disabled = true;
        };
        const resetWatchdog = () => {
            if (scanWatchdog) clearTimeout(scanWatchdog);
            if (!running) return;
            scanWatchdog = setTimeout(() => {
                setStatus('Scanning…');
                setFeedback('No QR detected yet — center the code and hold steady.', 'warning');
            }, 4000);
        };
        const pickCamera = async () => {
            try {
                const cameras = await Html5Qrcode.getCameras();
                if (Array.isArray(cameras) && cameras.length) {
                    // Prefer back/rear camera; fall back to last in list (usually rear on mobile)
                    const back = cameras.find(c => /back|rear|environment/i.test(c.label || ''))
                        || cameras[cameras.length - 1];
                    selectedCameraLabel = back.label || 'camera';
                    return back.id;
                }
            } catch (_) {}
            // Fallback: let the browser pick the environment-facing camera
            return { facingMode: { ideal: 'environment' } };
        };
        const parsePayload = (raw) => {
            try { const p = JSON.parse(raw); if (p && typeof p === 'object' && p.code) return p; } catch (_) {}
            return null;
        };

        // ── Core: handle a decoded QR ──────────────────────────────────
        const applyCode = (decodedText) => {
            const raw     = (decodedText || '').trim();
            const payload = parsePayload(raw);
            const code    = (payload ? String(payload.code || '') : raw).trim().toUpperCase();
            if (!code || code === lastCode || scanLocked) return;
            lastCode = code;

            if (scannerOnly) {
                // ── AJAX path: no page reload ──
                scanLocked = true;
                setFeedback('Checking…', 'info');
                fetch(scanJsonUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept':       'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({
                        selected_event_id: eventSelect.value,
                        ticket_code:       code,
                        scanned_payload:   payload ? JSON.stringify(payload) : raw,
                        device_id:         getDeviceId(),
                    }),
                })
                .then(r => {
                    if (r.status === 401 || r.status === 419) {
                        // Session expired — send to login preserving the scanner URL
                        window.location.href = @json(route('filament.admin.auth.login'))
                            + '?intended=' + encodeURIComponent(window.location.href);
                        return null;
                    }
                    return r.json();
                })
                .then(result => {
                    if (!result) return;
                    showOverlay(result);
                    setFeedback(
                        result.ok ? '✓ ' + result.message : '✗ ' + (result.message || 'Denied'),
                        result.ok ? 'success' : (result.reason === 'already_used' || result.reason === 'wrong_event' ? 'warning' : 'error')
                    );
                })
                .catch(() => {
                    showOverlay({ ok: false, reason: 'network', message: 'Network error.', detail: 'Check your connection and try again.' });
                    setFeedback('Network error.', 'error');
                    scanLocked = false;
                });
            } else {
                // ── Normal path: fill form + submit ──
                if (codeInput)    codeInput.value    = code;
                if (payloadInput) payloadInput.value = payload ? JSON.stringify(payload) : raw;
                setStatus('Detected: ' + code);
                setFeedback('QR detected. Submitting…', 'info');
                if (submitBtn) submitBtn.click();
            }
        };

        // ── Scanner start / stop ───────────────────────────────────────
        const startScanner = async () => {
            if (running) return;
            if (!hasSelectedEvent()) {
                setStatus('Blocked');
                setFeedback('Choose the gate event before starting the scanner.', 'error');
                syncScannerButtons(); eventSelect.focus(); return;
            }
            if (isEmbedded && !scannerOnly) {
                const url = fullScannerBaseUrl + '&event_id=' + encodeURIComponent(String(eventSelect.value || ''));
                // Can't navigate parent directly from a sandboxed iframe — use postMessage
                window.parent.postMessage({ type: 'wado-navigate', url }, window.location.origin);
                return;
            }
            try {
                setStatus('Starting…'); setFeedback('Starting camera — allow access if prompted.', 'info');
                const cameraId = await pickCamera();
                scanner = new Html5Qrcode(readerTargetId);
                await scanner.start(
                    cameraId,
                    {
                        fps: 25,
                        qrbox: (w, h) => { const s = Math.floor(Math.min(w, h) * 0.72); return { width: s, height: s }; },
                        rememberLastUsedCamera: true,
                        showTorchButtonIfSupported: true,
                        showZoomSliderIfSupported: true,
                    },
                    (decoded) => { resetWatchdog(); applyCode(decoded); },
                    () => {}
                );
                running = true;
                statusEl.textContent = 'Scanning';
                statusEl.className   = 'vp-badge badge-scan';
                setFeedback('Live — hold QR inside the scan area.', 'info');
                syncScannerButtons(); resetWatchdog();

                // Go fullscreen on mobile to remove browser chrome
                if (scannerOnly) {
                    try { document.documentElement.requestFullscreen?.(); } catch (_) {}
                }
            } catch (err) {
                const msg = (err?.message || String(err)).toLowerCase();
                let hint = 'Check camera permissions and try again.';
                if (msg.includes('permission') || msg.includes('denied')) hint = 'Camera permission denied. Allow camera access in browser settings.';
                else if (msg.includes('notfound') || msg.includes('no camera')) hint = 'No camera found on this device.';
                else if (msg.includes('inuse') || msg.includes('already')) hint = 'Camera is in use by another app. Close it and retry.';
                setStatus('Error');
                setFeedback('Camera failed: ' + hint, 'error');
                scanner = null; syncScannerButtons();
            }
        };

        const stopScanner = async () => {
            if (!scanner || !running) return;
            try { await scanner.stop(); await scanner.clear(); }
            finally {
                scanner = null; running = false;
                if (scanWatchdog) { clearTimeout(scanWatchdog); scanWatchdog = null; }
                statusEl.textContent = 'Idle'; statusEl.className = 'vp-badge badge-idle';
                setFeedback(
                    hasSelectedEvent() ? 'Stopped. Start again when ready.' : 'Choose an event first.',
                    hasSelectedEvent() ? 'neutral' : 'warning'
                );
                syncScannerButtons();
            }
        };

        // ── Sync top-bar event select → all hidden event id fields ──────
        const syncEventFields = () => {
            const val = eventSelect?.value || '';
            document.querySelectorAll('#verify-form-event-id, .js-sync-event-id')
                .forEach(el => { el.value = val; });
        };
        if (eventSelect) eventSelect.addEventListener('change', syncEventFields);

        // ── Fallback toggle (mobile) ───────────────────────────────────
        const fallbackToggle = document.getElementById('vp-fallback-toggle');
        const fallbackBody   = document.getElementById('vp-fallback-body');
        if (fallbackToggle && fallbackBody) {
            // Auto-open if page has a verification result
            if (fallbackBody.querySelector('.vp-result')) {
                fallbackBody.classList.add('open');
                fallbackToggle.setAttribute('aria-expanded', 'true');
            }
            fallbackToggle.addEventListener('click', () => {
                const open = fallbackBody.classList.toggle('open');
                fallbackToggle.setAttribute('aria-expanded', String(open));
            });
        }

        startBtn.addEventListener('click', startScanner);
        stopBtn.addEventListener('click', stopScanner);
        eventSelect.addEventListener('change', () => {
            if (running) { setFeedback('Event changed — stop and restart the scanner.', 'warning'); return; }
            setStatus(hasSelectedEvent() ? 'Ready' : 'Idle');
            setFeedback(
                hasSelectedEvent() ? 'Event selected. Start the camera.' : 'Choose an event first.',
                hasSelectedEvent() ? 'neutral' : 'warning'
            );
            syncScannerButtons();
        });
        window.addEventListener('beforeunload', stopScanner);

        // initial state
        if (!hasSelectedEvent()) {
            setStatus('Waiting'); setFeedback('Choose an event first, then start the camera.', 'warning');
        } else {
            setStatus('Ready'); setFeedback('Event selected. Start the camera to begin scanning.', 'neutral');
        }
        syncScannerButtons();

        // ── Offline tools ──────────────────────────────────────────────
        let offlineRows = [];
        const renderOffline = () => {
            if (!offlineTableWrap) return;
            const q = (offlineSearch?.value || '').trim().toLowerCase();
            const rows = offlineRows.filter(r => !q || [r.code, r.name, r.event, r.phone, r.email].filter(Boolean).join(' ').toLowerCase().includes(q)).slice(0, 50);
            if (!rows.length) { offlineTableWrap.innerHTML = '<p style="color:var(--muted);margin:.5rem 0 0;font-size:.83rem;">No offline matches.</p>'; return; }
            offlineTableWrap.innerHTML = `<div class="table-wrap" style="margin-top:.65rem;"><table class="vp-table">
                <thead><tr><th>Code</th><th>Name</th><th>Phone/Email</th><th>Event</th><th>Purchased</th></tr></thead>
                <tbody>${rows.map(r => `<tr><td class="mono">${r.code||''}</td><td>${r.name||''}</td><td>${r.phone||r.email||''}</td><td>${r.event||''}</td><td>${r.purchased_at||''}</td></tr>`).join('')}</tbody>
            </table></div>`;
        };
        offlineSearch?.addEventListener('input', renderOffline);
        offlineFile?.addEventListener('change', async (e) => {
            const file = e.target.files?.[0]; if (!file) return;
            const parsed = JSON.parse(await file.text());
            offlineRows = Array.isArray(parsed?.rows) ? parsed.rows : [];
            renderOffline();
        });
    })();
    </script>
@endsection