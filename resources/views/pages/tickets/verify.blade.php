@extends('layouts.app')

@section('content')
    @php
        $verification = session('verification');
        $lookupResults = collect(session('lookup_results', []));
        $payload = is_array($verification['payload'] ?? null) ? $verification['payload'] : null;
        $events = collect($events ?? []);
        $selectedEventId = (int) old('selected_event_id', $selectedEventId ?? 0);
    @endphp

    <section class="verify-page">
        <div class="verify-shell">
            <h1>Ticket Verification</h1>
            <p>Scan QR payload, verify online, or verify from offline export data when internet is down.</p>

            <section class="verify-scanner">
                <div class="verify-scanner-head">
                    <strong>Camera Scanner</strong>
                    <span id="scanner-status">Idle</span>
                </div>
                <div id="qr-reader"></div>
                <div class="verify-scanner-actions">
                    <button type="button" id="start-scan">Start camera</button>
                    <button type="button" id="stop-scan" class="ghost" disabled>Stop camera</button>
                </div>
            </section>

            <form method="POST" action="{{ route('tickets.verify.store') }}" class="verify-form">
                @csrf
                <input type="hidden" id="scanned-payload" name="scanned_payload" value="{{ old('scanned_payload') }}">
                <input type="hidden" id="device-id" name="device_id" value="{{ old('device_id') }}">

                <label>
                    <span>Gate event</span>
                    <select name="selected_event_id" required>
                        <option value="">Select event...</option>
                        @foreach ($events as $event)
                            <option value="{{ $event->id }}" @selected($selectedEventId === (int) $event->id)>
                                {{ $event->title }} ({{ $event->starts_at?->format('d M, H:i') }})
                            </option>
                        @endforeach
                    </select>
                </label>

                <label>
                    <span>Ticket code</span>
                    <input id="ticket-code-input" type="text" name="ticket_code" value="{{ old('ticket_code') }}" placeholder="WADO-1-7-ABC123" required>
                </label>

                <label class="verify-checkbox">
                    <input type="checkbox" name="mark_as_used" value="1" checked>
                    <span>Mark as used after successful verification</span>
                </label>

                <label class="verify-checkbox">
                    <input id="auto-submit" type="checkbox" value="1" checked>
                    <span>Auto-submit after scan</span>
                </label>

                <button id="verify-submit" type="submit">Verify ticket</button>
            </form>

            <form method="POST" action="{{ route('tickets.verify.store') }}" class="verify-form lookup-form">
                @csrf
                <input type="hidden" name="selected_event_id" value="{{ $selectedEventId }}">
                <label>
                    <span>Find by name, phone, or email</span>
                    <input type="text" name="lookup" value="{{ old('lookup') }}" placeholder="Search attendee by name / phone / email">
                </label>
                <button type="submit" class="ghost-btn">Search attendee</button>
            </form>

            <section class="offline-tools">
                <div class="offline-head">
                    <strong>Offline verification tools</strong>
                    @if ($selectedEventId > 0)
                        <a href="{{ route('tickets.verify.export', ['event_id' => $selectedEventId]) }}">Download valid tickets export</a>
                    @else
                        <span>Select an event to download export</span>
                    @endif
                </div>
                <div class="offline-row">
                    <input type="file" id="offline-file" accept="application/json">
                    <input type="text" id="offline-search" placeholder="Search offline data by code / name / phone">
                </div>
                <div id="offline-table-wrap"></div>
            </section>

            @if ($verification)
                <article class="verify-result {{ $verification['ok'] ? 'ok' : 'bad' }}">
                    <strong>{{ $verification['ok'] ? 'Valid ticket' : 'Invalid ticket' }}</strong>
                    <p>{{ $verification['message'] }}</p>

                    @if (! empty($verification['ticket']))
                        <ul>
                            <li><span>Ticket code:</span> {{ $verification['ticket']->ticket_code }}</li>
                            <li><span>Event:</span> {{ $verification['ticket']->event->title }}</li>
                            <li><span>Holder:</span> {{ $verification['ticket']->user->name }}</li>
                            <li><span>Phone:</span> {{ $verification['ticket']->user->phone ?: 'N/A' }}</li>
                            <li><span>Status:</span> {{ ucfirst((string) $verification['ticket']->status) }}</li>
                        </ul>
                    @endif
                </article>
            @endif

            @if ($payload)
                <section class="payload-view">
                    <h2>Scanned QR payload (read-only)</h2>
                    <table>
                        <tbody>
                            @foreach ($payload as $key => $value)
                                <tr>
                                    <th>{{ $key }}</th>
                                    <td>{{ is_scalar($value) ? (string) $value : json_encode($value) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </section>
            @endif

            @if ($lookupResults->isNotEmpty())
                <section class="payload-view">
                    <h2>Name / Phone matches</h2>
                    <table>
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
                                    <td>{{ $ticket->ticket_code }}</td>
                                    <td>{{ $ticket->user->name }}</td>
                                    <td>{{ $ticket->user->phone ?: 'N/A' }}</td>
                                    <td>{{ $ticket->event->title }}</td>
                                    <td>{{ ucfirst((string) $ticket->status) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </section>
            @endif
        </div>
    </section>

    <style>
        .verify-page { min-height: 100vh; background: #f2f6fb; padding: 8rem 1rem 3rem; }
        .verify-shell { width: min(760px, calc(100% - 2rem)); margin: 0 auto; background: #fff; border: 1px solid #dce5f1; border-radius: 16px; padding: 1.5rem; }
        .verify-shell h1 { margin: 0; color: #0f1d33; }
        .verify-shell p { color: #000000; }
        .verify-scanner { margin-top: 1rem; border: 1px solid #d7e3f3; border-radius: 12px; padding: 0.9rem; background: #f8fbff; }
        .verify-scanner-head { display: flex; align-items: center; justify-content: space-between; gap: 0.75rem; margin-bottom: 0.6rem; color: #1a355a; }
        #scanner-status { font-size: 0.8rem; color: #6a7c96; }
        #qr-reader { width: 100%; min-height: 240px; border: 1px dashed #c5d4ea; border-radius: 10px; overflow: hidden; background: #fff; }
        .verify-scanner-actions { margin-top: 0.65rem; display: flex; gap: 0.55rem; }
        .verify-scanner-actions button { height: 38px; border: 0; border-radius: 8px; background: #1f69d6; color: #fff; font-weight: 700; cursor: pointer; padding: 0 0.85rem; }
        .verify-scanner-actions .ghost { background: #e6edf8; color: #375682; }
        .verify-form { display: grid; gap: 0.9rem; margin-top: 1rem; }
        .verify-form label { display: grid; gap: 0.35rem; color: #22344f; font-weight: 700; }
        .verify-form input[type='text'], .verify-form select { height: 42px; border: 1px solid #cfdced; border-radius: 8px; padding: 0 0.8rem; }
        .verify-checkbox { display: flex; align-items: center; gap: 0.55rem; font-weight: 500; }
        .verify-form button { height: 42px; border: 0; border-radius: 8px; background: #1f69d6; color: #fff; font-weight: 700; cursor: pointer; }
        .lookup-form { margin-top: 0.8rem; }
        .lookup-form .ghost-btn { background: #eff4fc; color: #26486f; border: 1px solid #cedcef; }
        .offline-tools { margin-top: 1rem; border: 1px solid #d7e3f3; border-radius: 12px; padding: 0.9rem; background: #fff; }
        .offline-head { display: flex; justify-content: space-between; align-items: center; gap: 0.8rem; }
        .offline-head a { color: #1f69d6; font-weight: 700; text-decoration: none; }
        .offline-row { margin-top: 0.65rem; display: grid; gap: 0.6rem; grid-template-columns: 1fr 1fr; }
        .offline-row input { height: 42px; border: 1px solid #cfdced; border-radius: 8px; padding: 0 0.8rem; }
        #offline-file { padding: 0.55rem 0.6rem; }
        #offline-table-wrap { margin-top: 0.7rem; }
        .payload-view { margin-top: 1rem; border: 1px solid #d7e3f3; border-radius: 12px; padding: 0.9rem; background: #fff; }
        .payload-view h2 { margin: 0 0 0.65rem; color: #17375f; font-size: 0.96rem; }
        .payload-view table { width: 100%; border-collapse: collapse; }
        .payload-view th, .payload-view td { border: 1px solid #e3ebf6; padding: 0.45rem 0.5rem; text-align: left; font-size: 0.84rem; color: #244166; vertical-align: top; }
        .payload-view th { background: #f6f9ff; width: 30%; }
        .verify-result { margin-top: 1rem; border-radius: 10px; padding: 0.9rem 1rem; }
        .verify-result.ok { border: 1px solid #bde7cb; background: #f2fbf5; color: #16643d; }
        .verify-result.bad { border: 1px solid #f7c2c8; background: #fff5f6; color: #8d1f2f; }
        .verify-result p { margin: 0.3rem 0 0; color: inherit; }
        .verify-result ul { margin: 0.65rem 0 0; padding: 0; list-style: none; display: grid; gap: 0.25rem; }
        .verify-result li span { font-weight: 700; }
        @media (max-width: 780px) {
            .offline-row { grid-template-columns: 1fr; }
        }
    </style>

    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script>
        (function () {
            const readerTargetId = 'qr-reader';
            const statusEl = document.getElementById('scanner-status');
            const startBtn = document.getElementById('start-scan');
            const stopBtn = document.getElementById('stop-scan');
            const codeInput = document.getElementById('ticket-code-input');
            const payloadInput = document.getElementById('scanned-payload');
            const deviceInput = document.getElementById('device-id');
            const autoSubmit = document.getElementById('auto-submit');
            const submitBtn = document.getElementById('verify-submit');
            const offlineFile = document.getElementById('offline-file');
            const offlineSearch = document.getElementById('offline-search');
            const offlineTableWrap = document.getElementById('offline-table-wrap');

            if (!startBtn || !stopBtn || !codeInput || !statusEl || typeof Html5Qrcode === 'undefined') {
                return;
            }

            let scanner = null;
            let running = false;
            let lastCode = '';

            const setStatus = (text) => {
                statusEl.textContent = text;
            };

            const parsePayload = (raw) => {
                try {
                    const parsed = JSON.parse(raw);
                    if (parsed && typeof parsed === 'object' && parsed.code) {
                        return parsed;
                    }
                } catch (error) {
                }

                return null;
            };

            const applyCode = (decodedText) => {
                const raw = (decodedText || '').trim();
                const payload = parsePayload(raw);
                const code = (payload ? String(payload.code || '') : raw).trim().toUpperCase();
                if (!code || code === lastCode) {
                    return;
                }
                lastCode = code;
                codeInput.value = code;
                if (payloadInput) {
                    payloadInput.value = payload ? JSON.stringify(payload) : raw;
                }
                setStatus('Code detected: ' + code);

                if (autoSubmit && autoSubmit.checked && submitBtn) {
                    submitBtn.click();
                }
            };

            const startScanner = async () => {
                if (running) return;

                scanner = new Html5Qrcode(readerTargetId);

                try {
                    await scanner.start(
                        { facingMode: 'environment' },
                        {
                            fps: 10,
                            qrbox: { width: 220, height: 220 },
                        },
                        (decodedText) => applyCode(decodedText),
                        () => {}
                    );

                    running = true;
                    startBtn.disabled = true;
                    stopBtn.disabled = false;
                    setStatus('Scanning...');
                } catch (error) {
                    setStatus('Camera start failed. Check camera permission.');
                    scanner = null;
                }
            };

            const stopScanner = async () => {
                if (!scanner || !running) return;

                try {
                    await scanner.stop();
                    await scanner.clear();
                } finally {
                    scanner = null;
                    running = false;
                    startBtn.disabled = false;
                    stopBtn.disabled = true;
                    setStatus('Idle');
                }
            };

            startBtn.addEventListener('click', startScanner);
            stopBtn.addEventListener('click', stopScanner);
            window.addEventListener('beforeunload', stopScanner);

            const getDeviceId = () => {
                const key = 'ticket_verify_device_id';
                let value = localStorage.getItem(key);
                if (!value) {
                    value = 'scanner-' + Math.random().toString(36).slice(2, 10);
                    localStorage.setItem(key, value);
                }
                return value;
            };

            if (deviceInput) {
                deviceInput.value = getDeviceId();
            }

            let offlineRows = [];
            const renderOffline = () => {
                if (!offlineTableWrap) return;
                const q = (offlineSearch?.value || '').trim().toLowerCase();
                const rows = offlineRows.filter((row) => {
                    if (!q) return true;
                    return [row.code, row.name, row.event, row.phone, row.email]
                        .filter(Boolean)
                        .join(' ')
                        .toLowerCase()
                        .includes(q);
                }).slice(0, 50);

                if (!rows.length) {
                    offlineTableWrap.innerHTML = '<p style="color:#6a7c96;margin:0;">No offline matches.</p>';
                    return;
                }

                const body = rows.map((row) => `
                    <tr>
                        <td>${row.code || ''}</td>
                        <td>${row.name || ''}</td>
                        <td>${row.phone || row.email || ''}</td>
                        <td>${row.event || ''}</td>
                        <td>${row.purchased_at || ''}</td>
                    </tr>
                `).join('');

                offlineTableWrap.innerHTML = `
                    <table>
                        <thead>
                            <tr><th>Code</th><th>Name</th><th>Phone/Email</th><th>Event</th><th>Purchased</th></tr>
                        </thead>
                        <tbody>${body}</tbody>
                    </table>
                `;
            };

            if (offlineSearch) {
                offlineSearch.addEventListener('input', renderOffline);
            }

            if (offlineFile) {
                offlineFile.addEventListener('change', async (event) => {
                    const file = event.target.files?.[0];
                    if (!file) return;
                    const text = await file.text();
                    const parsed = JSON.parse(text);
                    offlineRows = Array.isArray(parsed?.rows) ? parsed.rows : [];
                    renderOffline();
                });
            }
        })();
    </script>
@endsection
