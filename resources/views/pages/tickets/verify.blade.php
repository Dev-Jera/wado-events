@extends('layouts.app')

@if (request()->boolean('embedded'))
    @section('fullbleed', '1')
@endif

@if (request()->boolean('scanner_only'))
    @section('fullbleed', '1')
@endif

@section('content')
    @php
        $verification    = session('verification');
        $lookupResults   = collect(session('lookup_results', []));
        $payload         = is_array($verification['payload'] ?? null) ? $verification['payload'] : null;
        $events          = collect($events ?? []);
        $verificationRows= collect($verificationRows ?? []);
        $selectedEventId = (int) old('selected_event_id', $selectedEventId ?? 0);
        $scannerOnly     = request()->boolean('scanner_only');
        $isEmbedded      = request()->boolean('embedded');
        $backUrl         = request('back') ?: url('/dashboard/scanner-page');
        $initialFeedback = $selectedEventId > 0 ? 'Event selected. Start camera to scan.' : 'Choose an event first, then start the camera.';
        $initialTone     = $selectedEventId > 0 ? 'neutral' : 'warning';
        $activeTab       = $verification ? 'manual' : ($lookupResults->isNotEmpty() ? 'lookup' : 'scanner');
    @endphp

    <section class="vp {{ $scannerOnly ? 'vp-scanner-only' : '' }}">
        <div class="vp-shell">

            {{-- ── PAGE HEADER ── --}}
            @if (!$isEmbedded && !$scannerOnly)
            <header class="vp-header">
                <div class="vp-header-badge">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2z"/><path d="M13 5v2"/><path d="M13 17v2"/><path d="M13 11v2"/></svg>
                </div>
                <div>
                    <h1>Ticket Verification</h1>
                    <p>Scan QR codes, verify by code, or look up attendees.</p>
                </div>
            </header>
            @endif

            {{-- ── EVENT SELECTOR BAR ── --}}
            @unless ($scannerOnly)
            <div class="vp-event-bar">
                <label class="vp-event-bar-label" for="selected-event-id">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2z"/></svg>
                    Gate Event
                </label>
                <select id="selected-event-id" name="selected_event_id" class="vp-input vp-event-bar-select">
                    <option value="">Select event…</option>
                    @foreach ($events as $event)
                        <option value="{{ $event->id }}" @selected($selectedEventId === (int) $event->id)>
                            {{ $event->title }} ({{ $event->starts_at?->format('d M, H:i') }})
                        </option>
                    @endforeach
                </select>
            </div>
            @endunless

            @if ($scannerOnly)
            {{-- ══ SCANNER-ONLY: fullscreen camera, no tabs ══ --}}
            <div class="vp-card scanner-card scanner-card-only">
                <div class="vp-card-head">
                    <div class="vp-card-title">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7V5a2 2 0 0 1 2-2h2"/><path d="M17 3h2a2 2 0 0 1 2 2v2"/><path d="M21 17v2a2 2 0 0 1-2 2h-2"/><path d="M7 21H5a2 2 0 0 1-2-2v-2"/><rect width="7" height="7" x="7" y="7" rx="1"/></svg>
                        Camera Scanner
                    </div>
                    <span id="scanner-status" class="vp-badge badge-idle">Idle</span>
                </div>
                <div id="scanner-feedback" class="scanner-feedback fb-{{ $initialTone }}">{{ $initialFeedback }}</div>
                <div class="scan-mode-row">
                    <span class="scan-mode-label">Mode:</span>
                    <div class="scan-mode-toggle">
                        <button type="button" id="mode-entry" class="scan-mode-btn active" data-mode="entry">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                            Entry
                        </button>
                        <button type="button" id="mode-exit" class="scan-mode-btn" data-mode="exit">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                            Exit
                        </button>
                    </div>
                </div>
                <div id="qr-reader" class="qr-viewport"></div>
                <div id="no-qr-hint" class="no-qr-hint no-qr-hidden">
                    <div class="no-qr-inner">
                        <svg width="58" height="58" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7V5a2 2 0 0 1 2-2h2"/><path d="M17 3h2a2 2 0 0 1 2 2v2"/><path d="M21 17v2a2 2 0 0 1-2 2h-2"/><path d="M7 21H5a2 2 0 0 1-2-2v-2"/><rect width="7" height="7" x="3" y="3" rx="1"/><rect width="7" height="7" x="14" y="3" rx="1"/><rect width="7" height="7" x="3" y="14" rx="1"/><rect width="2.5" height="2.5" x="5.5" y="5.5"/><rect width="2.5" height="2.5" x="16.5" y="5.5"/><rect width="2.5" height="2.5" x="5.5" y="16.5"/></svg>
                        <p>Aim camera at<br>ticket QR code</p>
                    </div>
                </div>
                <div class="scanner-actions">
                    <select id="so-event-select" class="so-event-select">
                        <option value="">— choose event to scan for —</option>
                        @foreach ($events as $event)
                            <option value="{{ $event->id }}" @selected($selectedEventId === (int) $event->id)>
                                {{ $event->title }} ({{ $event->starts_at?->format('d M, H:i') }})
                            </option>
                        @endforeach
                    </select>
                    <button type="button" id="start-scan" class="btn btn-primary">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="6 3 20 12 6 21 6 3"/></svg>
                        Start
                    </button>
                    <button type="button" id="stop-scan" class="btn btn-ghost" disabled>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect width="6" height="16" x="4" y="4" rx="1"/><rect width="6" height="16" x="14" y="4" rx="1"/></svg>
                        Stop
                    </button>
                    <button type="button" id="manual-entry-btn" class="btn btn-ghost">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4Z"/></svg>
                        Code
                    </button>
                    <a href="{{ $backUrl }}" class="btn btn-outline">← Back</a>
                </div>
            </div>

            @else
            {{-- ══ NORMAL MODE: tabs ══ --}}

            {{-- ── TAB NAV ── --}}
            <div class="vp-tabs" id="vp-tabs" role="tablist">
                <button type="button" class="vp-tab {{ $activeTab === 'scanner' ? 'active' : '' }}" data-tab="scanner" role="tab">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7V5a2 2 0 0 1 2-2h2"/><path d="M17 3h2a2 2 0 0 1 2 2v2"/><path d="M21 17v2a2 2 0 0 1-2 2h-2"/><path d="M7 21H5a2 2 0 0 1-2-2v-2"/><rect width="7" height="7" x="7" y="7" rx="1"/></svg>
                    Scanner
                </button>
                <button type="button" class="vp-tab {{ $activeTab === 'manual' ? 'active' : '' }}" data-tab="manual" role="tab">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4Z"/></svg>
                    Manual Entry
                </button>
                <button type="button" class="vp-tab {{ $activeTab === 'lookup' ? 'active' : '' }}" data-tab="lookup" role="tab">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                    Lookup
                </button>
                <button type="button" class="vp-tab" data-tab="offline" role="tab">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>
                    Offline
                </button>
                <button type="button" class="vp-tab" data-tab="feed" role="tab">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="2"/><path d="M16.24 7.76a6 6 0 0 1 0 8.49"/><path d="M7.76 7.76a6 6 0 0 0 0 8.49"/><path d="M20.66 3.34a12 12 0 0 1 0 16.97"/><path d="M3.34 3.34a12 12 0 0 0 0 16.97"/></svg>
                    Live Feed
                </button>
            </div>

            {{-- ── TAB: SCANNER ── --}}
            <div class="vp-panel {{ $activeTab !== 'scanner' ? 'vp-hidden' : '' }}" id="tab-scanner" role="tabpanel">
                <div class="vp-card scanner-card" style="max-width:680px;margin:0 auto;width:100%;">
                    <div class="vp-card-head">
                        <div class="vp-card-title">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7V5a2 2 0 0 1 2-2h2"/><path d="M17 3h2a2 2 0 0 1 2 2v2"/><path d="M21 17v2a2 2 0 0 1-2 2h-2"/><path d="M7 21H5a2 2 0 0 1-2-2v-2"/><rect width="7" height="7" x="7" y="7" rx="1"/></svg>
                            Camera Scanner
                        </div>
                        <span id="scanner-status" class="vp-badge badge-idle">Idle</span>
                    </div>
                    <div id="scanner-feedback" class="scanner-feedback fb-{{ $initialTone }}">{{ $initialFeedback }}</div>
                    <div class="scan-mode-row">
                        <span class="scan-mode-label">Mode:</span>
                        <div class="scan-mode-toggle">
                            <button type="button" id="mode-entry" class="scan-mode-btn active" data-mode="entry">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                                Entry
                            </button>
                            <button type="button" id="mode-exit" class="scan-mode-btn" data-mode="exit">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                                Exit
                            </button>
                        </div>
                    </div>
                    <div class="qr-area">
                        <div id="qr-reader" class="qr-viewport"></div>
                        <div id="no-qr-hint" class="no-qr-hint no-qr-hidden">
                            <div class="no-qr-inner">
                                <svg width="58" height="58" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7V5a2 2 0 0 1 2-2h2"/><path d="M17 3h2a2 2 0 0 1 2 2v2"/><path d="M21 17v2a2 2 0 0 1-2 2h-2"/><path d="M7 21H5a2 2 0 0 1-2-2v-2"/><rect width="7" height="7" x="3" y="3" rx="1"/><rect width="7" height="7" x="14" y="3" rx="1"/><rect width="7" height="7" x="3" y="14" rx="1"/><rect width="2.5" height="2.5" x="5.5" y="5.5"/><rect width="2.5" height="2.5" x="16.5" y="5.5"/><rect width="2.5" height="2.5" x="5.5" y="16.5"/></svg>
                                <p>Aim camera at<br>ticket QR code</p>
                            </div>
                        </div>
                    </div>
                    <div class="scanner-actions">
                        <button type="button" id="start-scan" class="btn btn-primary">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="6 3 20 12 6 21 6 3"/></svg>
                            Start camera
                        </button>
                        <button type="button" id="stop-scan" class="btn btn-ghost" disabled>
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect width="6" height="16" x="4" y="4" rx="1"/><rect width="6" height="16" x="14" y="4" rx="1"/></svg>
                            Stop camera
                        </button>
                    </div>
                </div>
            </div>

            {{-- ── TAB: MANUAL ENTRY (AJAX — no page reload) ── --}}
            <div class="vp-panel {{ $activeTab !== 'manual' ? 'vp-hidden' : '' }}" id="tab-manual" role="tabpanel">
                <div class="vp-card" style="max-width:520px;margin:0 auto;width:100%;">
                    <div class="vp-card-head">
                        <div class="vp-card-title">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                            Manual Entry
                        </div>
                    </div>
                    <div class="vp-form" style="margin-top:.1rem;">
                        <div class="field">
                            <label class="field-label" for="manual-tab-code">Ticket Code</label>
                            <div class="input-with-icon">
                                <svg class="input-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                <input id="manual-tab-code" type="text" class="vp-input has-icon" placeholder="WADO-1-7-ABC123" autocomplete="off" autocorrect="off" spellcheck="false" style="text-transform:uppercase;">
                            </div>
                        </div>
                        <div class="scan-mode-row">
                            <span class="scan-mode-label">Mode:</span>
                            <div class="scan-mode-toggle">
                                <button type="button" class="manual-mode-btn scan-mode-btn active" data-mode="entry">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                                    Entry
                                </button>
                                <button type="button" class="manual-mode-btn scan-mode-btn" data-mode="exit">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                                    Exit
                                </button>
                            </div>
                        </div>
                        <button id="manual-tab-submit" type="button" class="btn btn-primary btn-full">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                            Verify Ticket
                        </button>
                    </div>
                    <div id="manual-tab-result" style="margin-top:1rem;"></div>
                </div>
            </div>

            {{-- ── TAB: ATTENDEE LOOKUP ── --}}
            <div class="vp-panel {{ $activeTab !== 'lookup' ? 'vp-hidden' : '' }}" id="tab-lookup" role="tabpanel">
                <div class="vp-card" style="max-width:680px;margin:0 auto;width:100%;">
                    <div class="vp-card-head">
                        <div class="vp-card-title">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                            Attendee Lookup
                        </div>
                    </div>
                    <form method="POST" action="{{ route('tickets.verify.store') }}" class="vp-form">
                        @csrf
                        <input type="hidden" class="js-sync-event-id" name="selected_event_id" value="{{ $selectedEventId ?: '' }}">
                        <input type="hidden" name="embedded" value="{{ $isEmbedded ? '1' : '' }}">
                        <input type="hidden" name="back" value="{{ request('back') }}">
                        <div class="field">
                            <label class="field-label">Search by name, phone, or email</label>
                            <div class="input-with-icon">
                                <svg class="input-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                <input type="text" name="lookup" class="vp-input has-icon" value="{{ old('lookup') }}" placeholder="e.g. John Smith / +256…">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-full">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                            Search Attendee
                        </button>
                    </form>

                    @if ($lookupResults->isNotEmpty())
                    <div style="margin-top:1.25rem;">
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.65rem;">
                            <span class="vp-card-title" style="font-size:.8rem;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                Results
                            </span>
                            <span class="vp-badge badge-blue">{{ $lookupResults->count() }} found</span>
                        </div>
                        <div class="table-wrap">
                            <table class="vp-table">
                                <thead>
                                    <tr>
                                        <th>Code</th><th>Name</th><th>Phone</th><th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($lookupResults as $ticket)
                                    <tr>
                                        <td class="mono">{{ $ticket->ticket_code }}</td>
                                        <td>{{ $ticket->holder_name ?: $ticket->user->name }}</td>
                                        <td>{{ $ticket->user->phone ?: 'N/A' }}</td>
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
                </div>
            </div>

            {{-- ── TAB: OFFLINE TOOLS ── --}}
            <div class="vp-panel vp-hidden" id="tab-offline" role="tabpanel">
                <div class="vp-card" style="max-width:680px;margin:0 auto;width:100%;">
                    <div class="vp-card-head">
                        <div class="vp-card-title">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>
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
            </div>

            {{-- ── TAB: LIVE FEED ── --}}
            <div class="vp-panel vp-hidden" id="tab-feed" role="tabpanel">
                <div class="vp-card" style="max-width:680px;margin:0 auto;width:100%;" id="live-feed-card">
                    <div class="vp-card-head">
                        <div class="vp-card-title">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="2"/><path d="M16.24 7.76a6 6 0 0 1 0 8.49"/><path d="M7.76 7.76a6 6 0 0 0 0 8.49"/><path d="M20.66 3.34a12 12 0 0 1 0 16.97"/><path d="M3.34 3.34a12 12 0 0 0 0 16.97"/></svg>
                            Live Gate Feed
                        </div>
                        <span id="live-feed-ws-status" class="vp-badge badge-idle" style="font-size:.68rem">Offline</span>
                    </div>
                    <ul id="live-feed-list" class="lf-list">
                        <li id="lf-empty" class="lf-empty">Live scans from all gate devices will appear here once connected.</li>
                    </ul>
                </div>
            </div>

            {{-- ── QR PAYLOAD (shows when scan result has payload) ── --}}
            @if ($payload)
            <div class="vp-card">
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

            {{-- ── VERIFICATION AUDIT TABLE ── --}}
            <div class="vp-card vp-card-full">
                <div class="vp-card-head">
                    <div class="vp-card-title">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3h18v18H3z"/><path d="M3 9h18"/><path d="M9 21V9"/></svg>
                        Verification Audit
                    </div>
                    <span class="vp-muted">QR payload, signature checks, scan state</span>
                </div>
                @if ($selectedEventId <= 0)
                    <p class="vp-muted" style="margin:0;">Select a Gate Event above to load audit rows.</p>
                @elseif ($verificationRows->isEmpty())
                    <p class="vp-muted" style="margin:0;">No tickets found for this event.</p>
                @else
                    <div class="table-wrap">
                        <table class="vp-table">
                            <thead>
                                <tr>
                                    <th>Holder</th><th>Ticket Code</th><th>Payment</th>
                                    <th>QR Payload</th><th>Signature</th><th>Scan State</th>
                                    <th>Last Result</th><th>Last Signature</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($verificationRows as $row)
                                @php
                                    $ticket        = $row['ticket'];
                                    $payStatus     = strtoupper((string) ($ticket->paymentTransaction?->status ?? 'N/A'));
                                    $scanState     = ($ticket->used_at || $ticket->status === \App\Models\Ticket::STATUS_USED) ? 'Scanned' : 'Waiting';
                                    $lastScan      = $row['latest_scan'];
                                    $lastResult    = strtoupper((string) ($lastScan?->result ?? 'NOT_SCANNED'));
                                    $genSigOk      = (bool) ($row['generated_signature_ok'] ?? false);
                                    $lastSigOk     = $row['latest_signature_ok'];
                                @endphp
                                <tr>
                                    <td>{{ $ticket->holder_name ?: ($ticket->user?->name ?? 'N/A') }}</td>
                                    <td class="mono">{{ $ticket->ticket_code }}</td>
                                    <td>
                                        <span class="status-pill {{ $payStatus === \App\Models\PaymentTransaction::STATUS_CONFIRMED ? 'pill-valid' : 'pill-bad' }}">{{ $payStatus }}</span>
                                    </td>
                                    <td class="payload-cell">
                                        <details><summary>View</summary><pre>{{ $row['generated_payload_json'] }}</pre></details>
                                    </td>
                                    <td><span class="status-pill {{ $genSigOk ? 'pill-valid' : 'pill-bad' }}">{{ $genSigOk ? 'OK' : 'FAIL' }}</span></td>
                                    <td><span class="status-pill {{ $scanState === 'Scanned' ? 'pill-used' : 'pill-pending' }}">{{ $scanState }}</span></td>
                                    <td>
                                        <span class="status-pill {{ $lastResult === 'VALID' ? 'pill-valid' : ($lastResult === 'NOT_SCANNED' ? 'pill-pending' : 'pill-bad') }}">{{ $lastResult }}</span>
                                        @if ($lastScan?->scanned_at)
                                            <div class="vp-muted" style="margin-top:3px;font-size:.68rem;">{{ $lastScan->scanned_at->format('d M H:i:s') }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        @if (is_bool($lastSigOk))
                                            <span class="status-pill {{ $lastSigOk ? 'pill-valid' : 'pill-bad' }}">{{ $lastSigOk ? 'OK' : 'FAIL' }}</span>
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

            @endif {{-- end normal mode --}}

        </div>
    </section>

    {{-- ── MANUAL CODE OVERLAY (scanner_only fallback) ── --}}
    <div id="manual-overlay" class="mco mco-hidden" role="dialog" aria-modal="true">
        <div class="mco-card">
            <p class="mco-label">QR unreadable? Enter the code printed below the QR.</p>
            <input id="manual-code-input" class="mco-input" type="text" placeholder="WADO-1-2-ABC123" autocomplete="off" autocorrect="off" spellcheck="false">
            <div class="mco-actions">
                <button type="button" id="manual-cancel" class="mco-btn mco-btn-ghost">Cancel</button>
                <button type="button" id="manual-confirm" class="mco-btn mco-btn-primary">Verify</button>
            </div>
        </div>
    </div>

    {{-- ── FULLSCREEN SCAN RESULT OVERLAY (scanner_only) ── --}}
    <div id="scan-overlay" class="sco sco-hidden" role="alert" aria-live="assertive">
        <div class="sco-body">
            <div class="sco-icon-wrap" id="sco-icon-wrap"></div>
            <p class="sco-holder" id="sco-holder"></p>
            <p class="sco-event"  id="sco-event"></p>
            <p class="sco-msg"    id="sco-msg"></p>
            <p class="sco-code"   id="sco-code"></p>
        </div>
        <div class="sco-progress"><div class="sco-progress-fill" id="sco-progress-fill"></div></div>
    </div>

    <style>
        :root {
            --wado-admin-font: 'Quicksand','Nunito','Plus Jakarta Sans','Segoe UI',sans-serif;
            --blue:      #0A4FBE;
            --blue-dark: #083F98;
            --blue-light:#E8F0FF;
            --blue-mid:  #C9D9F8;
            --blue-tint: #F2F7FF;
            --white:     #FFFFFF;
            --off:       #EDF1FA;
            --border:    #D4DCF0;
            --text:      #111827;
            --muted:     #5A6A8A;
            --radius:    13px;
            --shadow:    0 2px 10px rgba(26,79,191,.1),0 1px 3px rgba(0,0,0,.06);
        }

        .vp {
            min-height: 100vh;
            background: var(--off);
            padding: 7rem 1rem 3rem;
            font-family: var(--wado-admin-font);
        }
        .vp, .vp *:not(svg):not(path):not(rect):not(circle):not(line):not(polyline):not(polygon),
        .vp button,.vp input,.vp select,.vp textarea,.vp table,.vp th,.vp td,
        .vp label,.vp span,.vp strong,.vp small,.vp a { font-family: var(--wado-admin-font); }

        @if ($isEmbedded) .vp { padding: 1rem; } @endif

        .vp-shell { width: min(1000px,100%); margin: 0 auto; display: flex; flex-direction: column; gap: 1.1rem; }

        /* ── scanner-only mode ── */
        .vp-scanner-only { min-height:100vh; min-height:100svh; padding:0; background:#000; }
        .vp-scanner-only .vp-shell { width:100%; min-height:100vh; min-height:100svh; margin:0; max-width:none; gap:0; }
        .vp-scanner-only .scanner-card { position:relative; border:0; border-radius:0; margin:0; padding:0; box-shadow:none; min-height:100vh; min-height:100svh; background:#000; }
        .vp-scanner-only .vp-card-head { position:absolute; top:0; left:0; right:0; z-index:6; margin:0; border-radius:0; padding:.65rem .8rem; border-bottom:0; background:linear-gradient(180deg,rgba(0,0,0,.7),rgba(0,0,0,0)); }
        .vp-scanner-only .vp-card-title,.vp-scanner-only .vp-badge { color:#fff; border-color:rgba(255,255,255,.35); background:rgba(0,0,0,.35); }
        .vp-scanner-only .scanner-feedback { position:absolute; top:3.1rem; left:.8rem; right:.8rem; z-index:6; margin:0; background:rgba(0,0,0,.55); color:#fff; border-color:rgba(255,255,255,.3); backdrop-filter:blur(2px); }
        .vp-scanner-only .qr-viewport { width:100%; min-height:100vh; min-height:100svh; height:100vh; height:100svh; border:0; border-radius:0; background:#000; }
        .vp-scanner-only .scanner-actions { position:absolute; bottom:1rem; left:.75rem; right:.75rem; z-index:6; margin:0; background:rgba(0,0,0,.55); border:1px solid rgba(255,255,255,.2); border-radius:12px; padding:.5rem; display:flex; gap:.4rem; flex-wrap:wrap; }
        .vp-scanner-only .scanner-actions .btn { flex:1; justify-content:center; min-width:0; font-size:.78rem; padding:0 .5rem; white-space:nowrap; }
        .vp-scanner-only .scan-mode-row { position:absolute; top:3.8rem; left:.8rem; right:.8rem; z-index:6; margin:0; }
        .scanner-card-only .qr-viewport { min-height:100vh; min-height:100svh; height:100vh; height:100svh; border:0; border-radius:0; }
        #qr-reader { width:100% !important; }
        #qr-reader video { width:100% !important; height:auto !important; object-fit:cover !important; display:block !important; }
        #qr-reader canvas { display:block !important; opacity:0.001 !important; pointer-events:none !important; }
        #qr-reader__scan_region { position:relative !important; }
        .vp-scanner-only #qr-reader { width:100% !important; height:100% !important; position:relative !important; overflow:hidden !important; }
        .vp-scanner-only #qr-reader video { position:absolute !important; inset:0 !important; width:100% !important; height:100% !important; object-fit:cover !important; z-index:1 !important; }
        .vp-scanner-only #qr-reader__scan_region { position:absolute !important; inset:0 !important; width:100% !important; height:100% !important; z-index:2 !important; overflow:hidden !important; }
        .vp-scanner-only #qr-reader canvas { position:absolute !important; inset:0 !important; width:100% !important; height:100% !important; z-index:3 !important; }
        .vp-scanner-only #qr-reader__scan_region > img { display:none !important; }

        /* ── header ── */
        .vp-header { display:flex; align-items:center; gap:1rem; background:linear-gradient(135deg,var(--blue) 0%,var(--blue-dark) 100%); border-radius:var(--radius); padding:1.4rem 1.6rem; color:var(--white); }
        .vp-header-badge { flex-shrink:0; width:44px; height:44px; border-radius:10px; background:rgba(255,255,255,.18); display:flex; align-items:center; justify-content:center; }
        .vp-header h1 { margin:0; font-size:1.25rem; font-weight:700; }
        .vp-header p  { margin:.2rem 0 0; font-size:.82rem; opacity:.8; }

        /* ── event bar ── */
        .vp-event-bar { display:flex; align-items:center; gap:.75rem; background:#fff; border:1.5px solid var(--border); border-radius:var(--radius); padding:.75rem 1.1rem; box-shadow:var(--shadow); }
        .vp-event-bar-label { display:flex; align-items:center; gap:.4rem; font-size:.78rem; font-weight:700; color:var(--blue); white-space:nowrap; flex-shrink:0; }
        .vp-event-bar-select { flex:1; margin:0; }

        /* ── tab nav ── */
        .vp-tabs { display:flex; gap:.3rem; background:#fff; border:1.5px solid var(--border); border-radius:var(--radius); padding:.35rem; box-shadow:var(--shadow); overflow-x:auto; scrollbar-width:none; }
        .vp-tabs::-webkit-scrollbar { display:none; }
        .vp-tab { display:inline-flex; align-items:center; gap:.4rem; white-space:nowrap; height:36px; padding:0 .9rem; border:none; border-radius:8px; font-size:.82rem; font-weight:700; cursor:pointer; background:transparent; color:var(--muted); transition:.15s; flex-shrink:0; }
        .vp-tab.active { background:var(--blue); color:#fff; }
        .vp-tab:hover:not(.active) { background:var(--off); color:var(--text); }
        .vp-tab svg { flex-shrink:0; }

        /* ── tab panels ── */
        .vp-panel { display:flex; flex-direction:column; gap:1rem; }
        .vp-hidden { display:none !important; }

        /* ── card ── */
        .vp-card { background:var(--white); border:1px solid var(--border); border-radius:var(--radius); padding:1.15rem 1.3rem; box-shadow:var(--shadow); }
        .vp-card-full { width:100%; }
        .vp-card-head { display:flex; align-items:center; justify-content:space-between; gap:.6rem; background:var(--blue-tint); margin:-1.15rem -1.3rem 1rem; padding:.75rem 1.3rem; border-radius:var(--radius) var(--radius) 0 0; border-bottom:1px solid var(--border); }
        .vp-card-title { display:flex; align-items:center; gap:.45rem; font-size:.82rem; font-weight:600; color:var(--blue); }

        /* ── scanner card ── */
        .scanner-card { border-top:3px solid var(--blue); position:relative; }
        .scanner-feedback { border-radius:8px; padding:.65rem .8rem; font-size:.83rem; font-weight:600; border:1px solid; margin-bottom:.85rem; }
        .fb-neutral { background:var(--blue-tint); color:var(--blue);    border-color:var(--blue-mid); }
        .fb-info    { background:var(--blue-light); color:#0e4fa8;        border-color:var(--blue-mid); }
        .fb-success { background:#F0FBF4;           color:#146c3a;        border-color:#B8E8CA; }
        .fb-warning { background:#FFFAED;           color:#8B5C00;        border-color:#F5DFA0; }
        .fb-error   { background:var(--blue-light); color:var(--blue-dark);border-color:var(--blue-mid); }
        .qr-viewport { width:100%; min-height:240px; border:2px dashed var(--blue-mid); border-radius:10px; background:var(--blue-tint); overflow:hidden; position:relative; }
        .scanner-actions { margin-top:.75rem; display:flex; gap:.5rem; flex-wrap:wrap; }

        /* ── scan mode toggle ── */
        .scan-mode-row { display:flex; align-items:center; gap:.6rem; margin-bottom:.75rem; }
        .scan-mode-label { font-size:.75rem; font-weight:700; color:var(--muted); white-space:nowrap; flex-shrink:0; }
        .scan-mode-toggle { display:flex; background:var(--off); border:1.5px solid var(--border); border-radius:8px; padding:3px; gap:3px; }
        .scan-mode-btn { display:inline-flex; align-items:center; gap:.35rem; height:30px; padding:0 .75rem; border:none; border-radius:6px; font-size:.78rem; font-weight:700; cursor:pointer; background:transparent; color:var(--muted); transition:.15s; }
        .scan-mode-btn.active[data-mode="entry"] { background:#16a34a; color:#fff; }
        .scan-mode-btn.active[data-mode="exit"]  { background:#0a4fbe; color:#fff; }
        .scan-mode-btn:not(.active):hover { background:var(--border); color:var(--text); }

        /* ── scanner-only event select ── */
        .so-event-select { flex:1 1 100%; height:36px; border:1.5px solid rgba(255,255,255,.35); border-radius:8px; background:rgba(255,255,255,.1); color:#fff; padding:0 .75rem; font-size:.82rem; font-weight:700; font-family:var(--wado-admin-font); cursor:pointer; outline:none; appearance:none; -webkit-appearance:none; }
        .so-event-select:focus { border-color:rgba(255,255,255,.75); }
        .so-event-select option { background:#1a2535; color:#e2e8f0; }
        .so-event-select.unset { border-color:#f59e0b !important; background:rgba(245,158,11,.18) !important; }

        /* ── no-QR detected hint ── */
        .qr-area { position:relative; overflow:hidden; border-radius:10px; }
        .no-qr-hint { position:absolute; inset:0; z-index:4; display:flex; align-items:center; justify-content:center; pointer-events:none; }
        .no-qr-hidden { display:none !important; }
        .no-qr-inner { display:flex; flex-direction:column; align-items:center; gap:.8rem; background:rgba(0,0,0,.62); border:2px dashed rgba(255,255,255,.38); border-radius:16px; padding:1.75rem 2.25rem; text-align:center; animation:noqr-pulse 2s ease-in-out infinite; }
        .no-qr-inner svg { stroke:rgba(255,255,255,.75); }
        .no-qr-inner p { margin:0; color:rgba(255,255,255,.88); font-size:1rem; font-weight:700; line-height:1.5; font-family:var(--wado-admin-font); }
        @keyframes noqr-pulse { 0%,100%{opacity:.65;transform:scale(1)} 50%{opacity:1;transform:scale(1.03)} }

        /* ── buttons ── */
        .btn { display:inline-flex; align-items:center; gap:.45rem; height:40px; padding:0 1rem; border:none; border-radius:8px; font-size:.84rem; font-weight:600; cursor:pointer; transition:.15s; }
        .btn-primary  { background:var(--blue); color:#fff; }
        .btn-primary:hover { background:var(--blue-dark); }
        .btn-ghost    { background:var(--blue-tint); color:var(--blue); border:1px solid var(--border); }
        .btn-ghost:hover { background:var(--blue-mid); }
        .btn-outline  { background:var(--blue-tint); color:var(--blue); border:1.5px solid var(--blue); }
        .btn-outline:hover { background:var(--blue-mid); }
        .btn-full     { width:100%; justify-content:center; }
        .btn[disabled]{ opacity:.4; cursor:not-allowed; }

        /* ── forms ── */
        .vp-form { display:flex; flex-direction:column; gap:.85rem; }
        .field { display:flex; flex-direction:column; gap:.35rem; }
        .field-label { font-size:.74rem; font-weight:600; color:var(--muted); }
        .vp-input { height:42px; border:1.5px solid var(--border); border-radius:8px; padding:0 .85rem; font-size:.875rem; color:var(--text); background:var(--blue-tint); transition:border-color .15s,background .15s; width:100%; box-sizing:border-box; }
        .vp-input:focus { outline:none; border-color:var(--blue); background:var(--white); }
        .input-with-icon { position:relative; }
        .input-icon { position:absolute; left:.75rem; top:50%; transform:translateY(-50%); color:var(--muted); pointer-events:none; }
        .vp-input.has-icon { padding-left:2.4rem; }

        /* ── offline ── */
        .offline-row { display:grid; grid-template-columns:1fr 1fr; gap:.65rem; margin-bottom:.75rem; }
        .file-label { display:flex; align-items:center; gap:.5rem; height:42px; border:1.5px solid var(--border); border-radius:8px; padding:0 .85rem; font-size:.83rem; font-weight:600; color:var(--blue); cursor:pointer; background:var(--blue-tint); white-space:nowrap; overflow:hidden; }
        .file-label:hover { background:var(--blue-mid); }
        .file-label input[type=file] { display:none; }

        /* ── live feed ── */
        .lf-list { list-style:none; margin:0; padding:0; display:grid; gap:.32rem; max-height:320px; overflow-y:auto; }
        .lf-empty { font-size:.75rem; color:var(--muted); padding:.4rem 0; text-align:center; }
        .lf-item  { display:flex; align-items:center; gap:.5rem; padding:.38rem .5rem; border-radius:8px; font-size:.78rem; }
        .lf-ok    { background:#f0faf4; }
        .lf-warn  { background:#fffbec; }
        .lf-bad   { background:#fff3f3; }
        .lf-icon  { font-size:.9rem; font-weight:700; width:1.1rem; text-align:center; flex-shrink:0; }
        .lf-ok  .lf-icon { color:#1a9e52; }
        .lf-warn.lf-icon { color:#c47d00; }
        .lf-bad .lf-icon { color:#c0392b; }
        .lf-body { flex:1; min-width:0; display:flex; flex-direction:column; gap:.05rem; }
        .lf-name { font-weight:600; color:#1a2535; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
        .lf-cat  { font-size:.69rem; color:#536684; }
        .lf-right{ display:flex; flex-direction:column; align-items:flex-end; gap:.05rem; flex-shrink:0; }
        .lf-time { font-size:.69rem; color:#7c8da7; font-variant-numeric:tabular-nums; }
        .lf-staff{ font-size:.65rem; color:#a0b0c0; }

        /* ── tables ── */
        .table-wrap { overflow-x:auto; border-radius:8px; border:1px solid var(--border); }
        .vp-table { width:100%; border-collapse:collapse; font-size:.83rem; }
        .vp-table thead th { background:var(--blue); color:#fff; padding:.5rem .75rem; text-align:left; font-size:.74rem; font-weight:600; }
        .vp-table tbody td,.vp-table tbody th { padding:.55rem .75rem; border-bottom:1px solid var(--border); color:var(--text); vertical-align:middle; }
        .vp-table tbody th { background:var(--blue-tint); font-weight:600; color:var(--muted); width:30%; }
        .vp-table tbody tr:last-child td,.vp-table tbody tr:last-child th { border-bottom:none; }
        .vp-table tbody tr:hover td { background:var(--blue-tint); }
        .mono { font-family:monospace; font-size:.8rem; color:var(--blue); font-weight:700; }
        .payload-cell details { max-width:330px; }
        .payload-cell summary { cursor:pointer; color:var(--blue); font-weight:600; }
        .payload-cell pre { margin-top:.35rem; background:#0f172a; color:#dbeafe; border-radius:6px; padding:.45rem .55rem; font-size:.72rem; overflow:auto; }

        /* ── status pills ── */
        .status-pill { display:inline-block; padding:.15rem .55rem; border-radius:100px; font-size:.75rem; font-weight:700; }
        .pill-valid   { background:var(--blue-light); color:var(--blue);  border:1px solid var(--blue-mid); }
        .pill-used    { background:#FFF0F0; color:#B01E1E;  border:1px solid #FCDCDC; }
        .pill-bad     { background:#FFF0F0; color:#B01E1E;  border:1px solid #FCDCDC; }
        .pill-pending { background:#EEF3FF; color:#1A4FBF;  border:1px solid #D0DCFA; }

        /* ── badges ── */
        .vp-badge { display:inline-flex; align-items:center; height:22px; border-radius:100px; padding:0 .6rem; font-size:.72rem; font-weight:600; }
        .badge-idle { background:var(--off); color:var(--muted); border:1px solid var(--border); }
        .badge-scan { background:var(--blue-light); color:var(--blue); border:1px solid var(--blue-mid); }
        .badge-blue { background:var(--blue-light); color:var(--blue); border:1px solid var(--blue-mid); }
        .vp-link  { display:inline-flex; align-items:center; gap:.35rem; color:var(--blue); font-size:.82rem; font-weight:700; text-decoration:none; }
        .vp-link:hover { text-decoration:underline; }
        .vp-muted { font-size:.8rem; color:var(--muted); }

        /* ── manual entry result ── */
        .vp-result { border-radius:10px; padding:1rem 1.1rem; border:1.5px solid; }
        .result-ok  { background:var(--blue-light); border-color:var(--blue-mid); color:var(--blue-dark); }
        .result-bad { background:#FFF0F0; border-color:#FCDCDC; color:#B01E1E; }
        .result-head { display:flex; align-items:center; gap:.5rem; font-weight:700; font-size:1rem; }
        .result-msg  { margin:.45rem 0 0; font-size:.875rem; opacity:.85; }
        .result-grid { margin:.75rem 0 0; display:grid; grid-template-columns:1fr 1fr; gap:.4rem .75rem; }
        .result-item { font-size:.83rem; color:var(--text); }
        .result-item span { display:block; font-size:.72rem; font-weight:600; opacity:.65; }

        /* ── fullscreen scan overlay ── */
        .sco { position:fixed; inset:0; z-index:9999; display:flex; flex-direction:column; align-items:center; justify-content:center; padding:2rem 1.5rem 0; transition:opacity .18s; }
        .sco-hidden  { opacity:0; pointer-events:none; }
        .sco-visible { opacity:1; pointer-events:auto; }
        .sco-ok   { background:#16a34a; }
        .sco-bad  { background:#c0283c; }
        .sco-warn { background:#b45309; }
        .sco-info { background:#0a4fbe; }
        .sco-body { flex:1; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:.75rem; text-align:center; }
        .sco-icon-wrap { width:90px; height:90px; border-radius:50%; background:rgba(255,255,255,.2); display:flex; align-items:center; justify-content:center; margin-bottom:.5rem; }
        .sco-icon-wrap svg { width:52px; height:52px; stroke:#fff; }
        .sco-holder { margin:0; font-size:clamp(1.6rem,8vw,2.6rem); font-weight:800; color:#fff; line-height:1.1; letter-spacing:-.02em; }
        .sco-event  { margin:0; font-size:clamp(.9rem,4vw,1.15rem); color:rgba(255,255,255,.82); font-weight:600; }
        .sco-msg    { margin:.25rem 0 0; font-size:clamp(.85rem,3.5vw,1rem); color:rgba(255,255,255,.7); font-weight:500; }
        .sco-code   { margin:0; font-family:monospace; font-size:clamp(.7rem,3vw,.88rem); color:rgba(255,255,255,.5); }
        .sco-progress { width:100%; height:5px; background:rgba(255,255,255,.2); position:absolute; bottom:0; left:0; }
        .sco-progress-fill { height:100%; background:rgba(255,255,255,.7); width:100%; transition:width linear; }

        /* ── manual code overlay ── */
        .mco { position:fixed; inset:0; z-index:10000; display:flex; align-items:center; justify-content:center; background:rgba(0,0,0,.65); padding:1.5rem; transition:opacity .15s; }
        .mco-hidden  { opacity:0; pointer-events:none; }
        .mco-visible { opacity:1; pointer-events:auto; }
        .mco-card { background:#fff; border-radius:16px; padding:1.4rem 1.5rem; width:min(380px,100%); display:flex; flex-direction:column; gap:.85rem; box-shadow:0 8px 32px rgba(0,0,0,.3); }
        .mco-label { margin:0; font-size:.88rem; font-weight:600; color:#132744; line-height:1.4; }
        .mco-input { height:48px; border:2px solid #dbe4f0; border-radius:10px; padding:0 1rem; font-size:1rem; font-weight:700; font-family:monospace; color:#132744; letter-spacing:.05em; text-transform:uppercase; transition:border-color .15s; }
        .mco-input:focus { outline:none; border-color:#0a4fbe; }
        .mco-actions { display:flex; gap:.6rem; }
        .mco-btn { flex:1; height:44px; border-radius:10px; font-size:.88rem; font-weight:700; cursor:pointer; border:none; transition:.15s; }
        .mco-btn-primary { background:#0a4fbe; color:#fff; }
        .mco-btn-primary:hover { background:#083f98; }
        .mco-btn-ghost { background:#f0f4ff; color:#0a4fbe; border:1.5px solid #dbe4f0; }
        .mco-btn-ghost:hover { background:#dbe4f0; }

        /* ── responsive ── */
        @media (max-width: 640px) {
            .vp-event-bar { flex-direction:column; align-items:stretch; }
            .vp-event-bar-label { justify-content:flex-start; }
            .offline-row { grid-template-columns:1fr; }
            .result-grid { grid-template-columns:1fr; }
            .vp-tab { padding:0 .65rem; font-size:.78rem; }
        }
    </style>

    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
    <script>
    (function () {
        const scannerOnly       = @json($scannerOnly);
        const isEmbedded        = @json($isEmbedded);
        const scanJsonUrl       = @json(route('tickets.verify.scan-json'));
        const csrfToken         = document.querySelector('meta[name=csrf-token]')?.content
                                  || document.cookie.match(/XSRF-TOKEN=([^;]+)/)?.[1]?.replace(/%3D/g,'=') || '';
        const fullScannerBaseUrl= @json(route('tickets.verify.index', ['scanner_only' => 1, 'back' => $backUrl]));
        const initialEventId    = @json((string) ($selectedEventId ?: ''));

        const readerTargetId = 'qr-reader';
        const statusEl       = document.getElementById('scanner-status');
        const feedbackEl     = document.getElementById('scanner-feedback');
        const startBtn       = document.getElementById('start-scan');
        const stopBtn        = document.getElementById('stop-scan');
        const eventSelect    = document.getElementById('selected-event-id');
        const offlineFile    = document.getElementById('offline-file');
        const offlineSearch  = document.getElementById('offline-search');
        const offlineTableWrap = document.getElementById('offline-table-wrap');

        const soEventSelect  = document.getElementById('so-event-select');
        const noQrHint       = document.getElementById('no-qr-hint');
        const showNoQrHint   = () => noQrHint?.classList.remove('no-qr-hidden');
        const hideNoQrHint   = () => noQrHint?.classList.add('no-qr-hidden');

        const overlay        = document.getElementById('scan-overlay');
        const scoIconWrap    = document.getElementById('sco-icon-wrap');
        const scoHolder      = document.getElementById('sco-holder');
        const scoEvent       = document.getElementById('sco-event');
        const scoMsg         = document.getElementById('sco-msg');
        const scoCode        = document.getElementById('sco-code');
        const scoProgressFill= document.getElementById('sco-progress-fill');

        let scanner = null, running = false, lastCode = '', scanLocked = false,
            scanWatchdog = null, selectedCameraLabel = '', overlayTimer = null;
        let selectedEventId = String(eventSelect?.value || initialEventId || '').trim();
        let scanMode = 'entry';

        // ── Tab switching ──────────────────────────────────────────────
        const tabs   = document.querySelectorAll('.vp-tab');
        const panels = document.querySelectorAll('.vp-panel');
        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                const target = tab.dataset.tab;
                tabs.forEach(t => t.classList.remove('active'));
                panels.forEach(p => p.classList.add('vp-hidden'));
                tab.classList.add('active');
                document.getElementById('tab-' + target)?.classList.remove('vp-hidden');
            });
        });

        // ── Scan mode toggle (camera scanner) ─────────────────────────
        const modeEntryBtn = document.getElementById('mode-entry');
        const modeExitBtn  = document.getElementById('mode-exit');
        const setScanMode = (mode) => {
            scanMode = mode;
            [modeEntryBtn, modeExitBtn].forEach(b => b?.classList.remove('active'));
            (mode === 'exit' ? modeExitBtn : modeEntryBtn)?.classList.add('active');
            if (running) setFeedback(mode === 'exit' ? 'EXIT mode — scan to record exit.' : 'ENTRY mode — scan to admit.', mode === 'exit' ? 'info' : 'neutral');
        };
        modeEntryBtn?.addEventListener('click', () => setScanMode('entry'));
        modeExitBtn?.addEventListener('click',  () => setScanMode('exit'));

        // ── Manual Entry Tab (AJAX) ────────────────────────────────────
        let manualTabMode = 'entry';
        const manualTabCode   = document.getElementById('manual-tab-code');
        const manualTabSubmit = document.getElementById('manual-tab-submit');
        const manualTabResult = document.getElementById('manual-tab-result');
        const manualModeBtns  = document.querySelectorAll('.manual-mode-btn');

        manualModeBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                manualTabMode = btn.dataset.mode;
                manualModeBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
            });
        });

        const showManualTabResult = (result) => {
            if (!manualTabResult) return;
            const isOk = !!result.ok;
            let html = `<div class="vp-result ${isOk ? 'result-ok' : 'result-bad'}">
                <div class="result-head">${isOk
                    ? '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg> Valid Ticket'
                    : '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" x2="9" y1="9" y2="15"/><line x1="9" x2="15" y1="9" y2="15"/></svg> Invalid Ticket'
                }</div>
                <p class="result-msg">${result.message || ''}</p>`;
            if (isOk && (result.holder || result.event || result.category)) {
                html += `<div class="result-grid">`;
                if (result.holder)   html += `<div class="result-item"><span>Holder</span>${result.holder}</div>`;
                if (result.event)    html += `<div class="result-item"><span>Event</span>${result.event}</div>`;
                if (result.category) html += `<div class="result-item"><span>Category</span>${result.category}</div>`;
                if (result.code)     html += `<div class="result-item"><span>Code</span><code>${result.code}</code></div>`;
                html += `</div>`;
            }
            html += '</div>';
            manualTabResult.innerHTML = html;
            playSound(isOk);
        };

        const submitManualTab = () => {
            const code = (manualTabCode?.value || '').trim().toUpperCase();
            if (!code) { manualTabCode?.focus(); return; }
            if (!selectedEventId) {
                if (manualTabResult) manualTabResult.innerHTML = '<p class="vp-muted" style="margin:.5rem 0;">Please select a gate event first.</p>';
                return;
            }
            if (manualTabSubmit) { manualTabSubmit.disabled = true; manualTabSubmit.textContent = 'Checking…'; }
            fetch(scanJsonUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ selected_event_id: selectedEventId, ticket_code: code, device_id: getDeviceId(), scan_type: manualTabMode }),
            })
            .then(r => {
                if (r.status === 401 || r.status === 419) { window.location.reload(); return null; }
                return r.json();
            })
            .then(result => {
                if (!result) return;
                showManualTabResult(result);
            })
            .catch(() => {
                if (manualTabResult) manualTabResult.innerHTML = '<p class="vp-muted" style="margin:.5rem 0;">Network error. Check your connection.</p>';
            })
            .finally(() => {
                if (manualTabSubmit) {
                    manualTabSubmit.disabled = false;
                    manualTabSubmit.innerHTML = `<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg> Verify Ticket`;
                }
            });
        };

        manualTabSubmit?.addEventListener('click', submitManualTab);
        manualTabCode?.addEventListener('keydown', e => { if (e.key === 'Enter') submitManualTab(); });

        // ── Device ID ──────────────────────────────────────────────────
        const getDeviceId = () => {
            const key = 'ticket_verify_device_id';
            let v = localStorage.getItem(key);
            if (!v) { v = 'scanner-' + Math.random().toString(36).slice(2,10); localStorage.setItem(key, v); }
            return v;
        };

        // ── Audio feedback ─────────────────────────────────────────────
        const playSound = (ok) => {
            try {
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                if (ok) {
                    [[880,0,.12],[1320,.13,.15]].forEach(([freq,when,dur]) => {
                        const o = ctx.createOscillator(), g = ctx.createGain();
                        o.connect(g); g.connect(ctx.destination);
                        o.type='sine'; o.frequency.value=freq;
                        g.gain.setValueAtTime(.28,ctx.currentTime+when);
                        g.gain.exponentialRampToValueAtTime(.001,ctx.currentTime+when+dur);
                        o.start(ctx.currentTime+when); o.stop(ctx.currentTime+when+dur+.05);
                    });
                } else {
                    const o = ctx.createOscillator(), g = ctx.createGain();
                    o.connect(g); g.connect(ctx.destination);
                    o.type='sawtooth';
                    o.frequency.setValueAtTime(220,ctx.currentTime);
                    o.frequency.exponentialRampToValueAtTime(80,ctx.currentTime+.35);
                    g.gain.setValueAtTime(.35,ctx.currentTime);
                    g.gain.exponentialRampToValueAtTime(.001,ctx.currentTime+.35);
                    o.start(ctx.currentTime); o.stop(ctx.currentTime+.4);
                }
            } catch(_) {}
        };

        // ── Fullscreen overlay ─────────────────────────────────────────
        const OVERLAY_DURATION = 3000;
        const RESCAN_COOLDOWN  = 1500;
        let rescanCooldownTimer = null;

        const overlayColor = (result) => {
            if (result.reason === 'foreign')             return 'sco-warn';
            if (result.ok && result.reason === 'exited') return 'sco-info';
            if (result.ok)                               return 'sco-ok';
            if (['already_used','already_inside','not_inside','wrong_event','cooldown'].includes(result.reason)) return 'sco-warn';
            return 'sco-bad';
        };

        const showOverlay = (result) => {
            if (!overlay) return;
            clearTimeout(overlayTimer);
            overlay.className = 'sco sco-visible ' + overlayColor(result);
            scoIconWrap.innerHTML = result.ok
                ? `<svg viewBox="0 0 24 24" fill="none" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>`
                : `<svg viewBox="0 0 24 24" fill="none" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>`;
            const reasonLabel = (() => {
                if (result.reason === 'foreign')             return 'Not a WADO ticket.';
                if (result.ok && result.reason === 'exited') return 'Exit recorded.';
                if (result.ok)                               return result.message || 'Ticket verified.';
                if (result.reason === 'already_used')        return 'Already used.';
                if (result.reason === 'already_inside')      return 'Already inside.';
                if (result.reason === 'not_inside')          return 'Not inside venue.';
                if (result.reason === 'reentry_limit')       return 'Re-entry limit reached.';
                if (result.reason === 'cooldown')            return result.message || 'Cooldown active.';
                if (result.reason === 'wrong_event')         return 'Wrong event.';
                return 'Fake ticket.';
            })();
            scoHolder.textContent = reasonLabel;
            scoEvent.textContent  = result.ok ? (result.event ? 'for ' + result.event : '') : (result.holder || '');
            scoMsg.textContent    = result.ok ? (result.category || '') : (result.detail || '');
            scoCode.textContent   = result.code ? 'CODE: ' + result.code : '';
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
            clearTimeout(rescanCooldownTimer);
            if (overlay) overlay.className = 'sco sco-hidden';
            scanLocked = false;
            rescanCooldownTimer = setTimeout(() => { lastCode = ''; }, RESCAN_COOLDOWN);
        };
        if (overlay) overlay.addEventListener('click', hideOverlay);

        // ── Helpers ────────────────────────────────────────────────────
        const setStatus   = (text) => { if(statusEl) statusEl.textContent = text; };
        const setFeedback = (text, tone='neutral') => {
            if (!feedbackEl) return;
            feedbackEl.textContent = text;
            feedbackEl.className   = 'scanner-feedback fb-' + tone;
        };
        const hasSelectedEvent = () => String(selectedEventId || '').trim() !== '';
        const syncScannerButtons = () => {
            if (!startBtn || !stopBtn) return;
            if (running) { startBtn.disabled=true; stopBtn.disabled=false; return; }
            startBtn.disabled = !hasSelectedEvent();
            stopBtn.disabled  = true;
        };
        const resetWatchdog = () => {
            if (scanWatchdog) clearTimeout(scanWatchdog);
            hideNoQrHint();
            if (!running) return;
            scanWatchdog = setTimeout(() => {
                setStatus('Scanning…');
                setFeedback('No QR detected — aim camera directly at the ticket QR code.', 'warning');
                showNoQrHint();
            }, 4000);
        };
        const pickCamera = async () => {
            try {
                const cameras = await Html5Qrcode.getCameras();
                if (Array.isArray(cameras) && cameras.length) {
                    const back = cameras.find(c => /back|rear|environment/i.test(c.label||'')) || cameras[cameras.length-1];
                    selectedCameraLabel = back.label || 'camera';
                    return back.id;
                }
            } catch(_) {}
            return { facingMode: { ideal: 'environment' } };
        };
        const parsePayload = (raw) => {
            try { const p=JSON.parse(raw); if(p && typeof p==='object' && p.code) return p; } catch(_) {}
            return null;
        };
        const cameraPreflightError = () => {
            if (!window.isSecureContext) return 'Camera requires HTTPS.';
            if (!navigator.mediaDevices || typeof navigator.mediaDevices.getUserMedia !== 'function') {
                if (/simulator|emulator|headless|electron/i.test(navigator.userAgent||'') || Boolean(window.__nightmare) || window.top!==window.self)
                    return 'Camera API unavailable in this embedded/simulator context. Open in a real browser.';
                return 'This browser does not support camera APIs.';
            }
            return null;
        };

        // ── Core: handle decoded QR ────────────────────────────────────
        const applyCode = (decodedText) => {
            const raw     = (decodedText||'').trim();
            if (!raw) return;
            const payload = parsePayload(raw);
            // Foreign QR: not a WADO JSON payload AND not a bare WADO-X-X-XXXX code
            const isWadoCode = (s) => /^WADO-\d+-\d+-[A-Z0-9]{3,}$/i.test(s.trim());
            if (!payload && !isWadoCode(raw)) {
                if (raw === lastCode || scanLocked) return;
                lastCode = raw;
                scanLocked = true;
                hideNoQrHint();
                setFeedback('Foreign QR — not a WADO ticket.', 'warning');
                showOverlay({ ok:false, reason:'foreign', message:'Not a WADO ticket.', detail:'This QR code does not belong to WADO Ticketing.' });
                playSound(false);
                return;
            }
            const code    = (payload ? String(payload.code||'') : raw).trim().toUpperCase();
            if (!code || code===lastCode || scanLocked) return;
            hideNoQrHint();
            lastCode = code;
            scanLocked = true;
            setFeedback('Checking…','info');
            fetch(scanJsonUrl, {
                method: 'POST',
                headers: { 'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':csrfToken },
                body: JSON.stringify({ selected_event_id:selectedEventId, ticket_code:code, scanned_payload:payload?JSON.stringify(payload):raw, device_id:getDeviceId(), scan_type:scanMode }),
            })
            .then(r => {
                if (r.status===401||r.status===419) { window.location.href=@json(route('filament.admin.auth.login'))+'?intended='+encodeURIComponent(window.location.href); return null; }
                return r.json();
            })
            .then(result => {
                if (!result) return;
                showOverlay(result);
                setFeedback(result.ok?'✓ '+result.message:'✗ '+(result.message||'Denied'), result.ok?'success':(['already_used','wrong_event'].includes(result.reason)?'warning':'error'));
            })
            .catch(() => {
                showOverlay({ ok:false, reason:'network', message:'Network error.', detail:'Check your connection.' });
                setFeedback('Network error.','error');
                scanLocked=false;
            });
        };

        // ── Start/stop scanner ─────────────────────────────────────────
        const startScanner = async () => {
            if (running) return;
            if (!hasSelectedEvent()) {
                setStatus('Blocked'); setFeedback('Choose the gate event before starting.','error');
                syncScannerButtons(); eventSelect?.focus(); return;
            }
            const preflight = cameraPreflightError();
            if (preflight) { setStatus('Error'); setFeedback('Camera failed: '+preflight,'error'); return; }
            if (isEmbedded && !scannerOnly) {
                const url = fullScannerBaseUrl+'&event_id='+encodeURIComponent(selectedEventId);
                window.parent.postMessage({ type:'wado-navigate', url }, window.location.origin);
                return;
            }
            try {
                setStatus('Starting…'); setFeedback('Requesting camera access…','info');
                const cameraId = await pickCamera();
                setFeedback('Camera found — loading video…','info');
                scanner = new Html5Qrcode(readerTargetId);
                await scanner.start(cameraId, {fps:8,qrbox:(w,h)=>{const s=Math.floor(Math.min(w,h)*.82);return{width:s,height:s};},rememberLastUsedCamera:true,showTorchButtonIfSupported:true,showZoomSliderIfSupported:true},
                    (decoded)=>{resetWatchdog();applyCode(decoded)},()=>{});
                running=true;
                if(statusEl){statusEl.textContent='Scanning';statusEl.className='vp-badge badge-scan';}
                setFeedback('Live — hold QR inside the scan area.','info');
                syncScannerButtons(); resetWatchdog();
            } catch(err) {
                const msg=(err?.message||String(err)).toLowerCase();
                let hint='Check camera permissions and try again.';
                if(msg.includes('permission')||msg.includes('denied')) hint='Camera permission denied. Allow camera in browser settings.';
                else if(msg.includes('notfound')||msg.includes('no camera')) hint='No camera found on this device.';
                else if(msg.includes('inuse')||msg.includes('already')) hint='Camera in use by another app. Close it and retry.';
                setStatus('Error'); setFeedback('Camera failed: '+hint,'error');
                scanner=null; syncScannerButtons();
            }
        };

        const stopScanner = async () => {
            if (!scanner||!running) return;
            try { await scanner.stop(); await scanner.clear(); }
            finally {
                scanner=null; running=false;
                hideNoQrHint();
                if(scanWatchdog){clearTimeout(scanWatchdog);scanWatchdog=null;}
                if(statusEl){statusEl.textContent='Idle';statusEl.className='vp-badge badge-idle';}
                setFeedback(hasSelectedEvent()?'Stopped. Start again when ready.':'Choose an event first.',hasSelectedEvent()?'neutral':'warning');
                syncScannerButtons();
            }
        };

        // ── Sync event select → hidden fields ─────────────────────────
        const syncEventFields = () => {
            const val = eventSelect?.value || selectedEventId || '';
            selectedEventId = String(val).trim();
            document.querySelectorAll('.js-sync-event-id').forEach(el => { el.value=val; });
        };
        if (eventSelect) eventSelect.addEventListener('change', syncEventFields);
        syncEventFields();

        // ── Scanner-only event select ──────────────────────────────────
        if (soEventSelect) {
            soEventSelect.addEventListener('change', () => {
                selectedEventId = soEventSelect.value.trim();
                document.querySelectorAll('.js-sync-event-id').forEach(el => { el.value = selectedEventId; });
                soEventSelect.classList.toggle('unset', !selectedEventId);
                if (running) {
                    setFeedback('Event changed — stop and restart the scanner.', 'warning');
                } else {
                    setStatus(selectedEventId ? 'Ready' : 'Idle');
                    setFeedback(selectedEventId ? 'Event selected. Start the camera.' : 'Choose an event above, then start the camera.', selectedEventId ? 'neutral' : 'warning');
                }
                syncScannerButtons();
            });
            soEventSelect.classList.toggle('unset', !soEventSelect.value);
        }

        startBtn?.addEventListener('click', startScanner);
        stopBtn?.addEventListener('click', stopScanner);
        if (eventSelect) {
            eventSelect.addEventListener('change', () => {
                if (running) { setFeedback('Event changed — stop and restart the scanner.','warning'); return; }
                setStatus(hasSelectedEvent()?'Ready':'Idle');
                setFeedback(hasSelectedEvent()?'Event selected. Start the camera.':'Choose an event first.',hasSelectedEvent()?'neutral':'warning');
                syncScannerButtons();
            });
        }
        window.addEventListener('beforeunload', stopScanner);

        if (!hasSelectedEvent()) {
            setStatus('Waiting');
            setFeedback(scannerOnly ? 'Choose an event above, then start the camera.' : 'Choose an event first, then start the camera.', 'warning');
        } else {
            setStatus('Ready'); setFeedback('Event selected. Start the camera to begin scanning.','neutral');
        }
        syncScannerButtons();
        if (soEventSelect) soEventSelect.classList.toggle('unset', !soEventSelect.value);

        // ── Offline tools ──────────────────────────────────────────────
        let offlineRows = [];
        const renderOffline = () => {
            if (!offlineTableWrap) return;
            const q = (offlineSearch?.value||'').trim().toLowerCase();
            const rows = offlineRows.filter(r=>!q||[r.code,r.name,r.event,r.phone,r.email].filter(Boolean).join(' ').toLowerCase().includes(q)).slice(0,50);
            if (!rows.length) { offlineTableWrap.innerHTML='<p style="color:var(--muted);margin:.5rem 0 0;font-size:.83rem;">No offline matches.</p>'; return; }
            offlineTableWrap.innerHTML=`<div class="table-wrap" style="margin-top:.65rem;"><table class="vp-table">
                <thead><tr><th>Code</th><th>Name</th><th>Phone/Email</th><th>Event</th><th>Purchased</th></tr></thead>
                <tbody>${rows.map(r=>`<tr><td class="mono">${r.code||''}</td><td>${r.name||''}</td><td>${r.phone||r.email||''}</td><td>${r.event||''}</td><td>${r.purchased_at||''}</td></tr>`).join('')}</tbody>
            </table></div>`;
        };
        offlineSearch?.addEventListener('input', renderOffline);
        offlineFile?.addEventListener('change', async(e)=>{
            const file=e.target.files?.[0]; if(!file) return;
            const parsed=JSON.parse(await file.text());
            offlineRows=Array.isArray(parsed?.rows)?parsed.rows:[];
            renderOffline();
        });

        // ── Reverb WebSocket live feed ─────────────────────────────────
        const liveFeedList     = document.getElementById('live-feed-list');
        const liveFeedWsStatus = document.getElementById('live-feed-ws-status');
        const lfEmpty          = document.getElementById('lf-empty');
        let wsScansChannel=null, wsPusher=null;
        const escHtml = (s) => String(s??'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
        const prependScan = (data) => {
            if(!liveFeedList) return;
            if(lfEmpty) lfEmpty.remove();
            const cls=data.ok?'lf-ok':(data.result==='already_used'?'lf-warn':'lf-bad');
            const icon=data.ok?'✓':(data.result==='already_used'?'↩':'✗');
            const li=document.createElement('li');
            li.className='lf-item '+cls;
            li.innerHTML=`<span class="lf-icon">${icon}</span><span class="lf-body"><span class="lf-name">${escHtml(data.holder)}</span>${data.category?`<span class="lf-cat">${escHtml(data.category)}</span>`:''}</span><span class="lf-right"><span class="lf-time">${escHtml(data.scanned_at)}</span>${data.staff_name?`<span class="lf-staff">${escHtml(data.staff_name)}</span>`:''}</span>`;
            liveFeedList.prepend(li);
            while(liveFeedList.children.length>25) liveFeedList.removeChild(liveFeedList.lastChild);
        };
        const reverbKey    = @json(config('broadcasting.connections.reverb.key',''));
        const reverbHost   = @json(config('broadcasting.connections.reverb.options.host','127.0.0.1'));
        const reverbPort   = {{ (int) config('broadcasting.connections.reverb.options.port',8080) }};
        const reverbScheme = @json(config('broadcasting.connections.reverb.options.scheme','http'));
        const subscribeScans = (evtId) => {
            if(!wsPusher||!evtId) return;
            if(wsScansChannel) wsPusher.unsubscribe(wsScansChannel.name);
            wsScansChannel=wsPusher.subscribe('private-event.'+evtId+'.scans');
            wsScansChannel.bind('TicketScanned', prependScan);
        };
        if (reverbKey && window.Pusher) {
            wsPusher=new window.Pusher(reverbKey,{wsHost:reverbHost,wsPort:reverbPort,wssPort:reverbPort,forceTLS:reverbScheme==='https',enabledTransports:['ws','wss'],cluster:'',authEndpoint:'/broadcasting/auth',auth:{headers:{'X-CSRF-TOKEN':csrfToken}}});
            wsPusher.connection.bind('connected',()=>{if(liveFeedWsStatus){liveFeedWsStatus.textContent='Live';liveFeedWsStatus.className='vp-badge badge-scan';}});
            wsPusher.connection.bind('disconnected',()=>{if(liveFeedWsStatus){liveFeedWsStatus.textContent='Offline';liveFeedWsStatus.className='vp-badge badge-idle';}});
            if(selectedEventId) subscribeScans(selectedEventId);
            if(eventSelect) eventSelect.addEventListener('change',()=>subscribeScans(selectedEventId));
        }

        // ── Manual code overlay (scanner_only fallback) ────────────────
        const manualOverlay = document.getElementById('manual-overlay');
        const manualInput   = document.getElementById('manual-code-input');
        const manualEntryBtn= document.getElementById('manual-entry-btn');
        const manualConfirm = document.getElementById('manual-confirm');
        const manualCancel  = document.getElementById('manual-cancel');
        const openManual    = () => { if(!manualOverlay) return; manualOverlay.className='mco mco-visible'; if(manualInput){manualInput.value='';manualInput.focus();} };
        const closeManual   = () => { if(manualOverlay) manualOverlay.className='mco mco-hidden'; };
        const submitManual  = () => { const code=(manualInput?.value||'').trim().toUpperCase(); if(!code) return; closeManual(); applyCode(code); };
        if(manualEntryBtn) manualEntryBtn.addEventListener('click', openManual);
        if(manualCancel)   manualCancel.addEventListener('click', closeManual);
        if(manualConfirm)  manualConfirm.addEventListener('click', submitManual);
        if(manualInput)    manualInput.addEventListener('keydown',e=>{if(e.key==='Enter')submitManual();if(e.key==='Escape')closeManual();});
        if(manualOverlay)  manualOverlay.addEventListener('click',e=>{if(e.target===manualOverlay)closeManual();});
    })();
    </script>
@endsection
