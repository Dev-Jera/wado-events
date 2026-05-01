<x-filament-widgets::widget>
<div class="po">

    {{-- ── Filter bar ── --}}
    <div class="po-bar">
        <div class="po-bar-left">
            <div class="po-bar-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </div>
            <div>
                <span class="po-bar-kicker">Payment stats for</span>
                <strong class="po-bar-title">{{ $selectedEvent?->title ?? 'All Events' }}</strong>
                    <wire:loading wire:target="selectedEventId" class="po-loading">updating…</wire:loading>
            </div>
        </div>
        <div class="po-bar-right">
            <div class="po-field">
                <label class="po-field-label" for="po-evt">Filter by event</label>
                <div class="po-select-wrap">
                    <select id="po-evt" wire:model.live="selectedEventId">
                        <option value="0">All events</option>
                        @foreach ($events as $event)
                            <option value="{{ $event->id }}">{{ $event->title }}</option>
                        @endforeach
                    </select>
                    <svg class="po-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                </div>
            </div>
            <div class="po-sync-pill">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>
                <div>
                    <span>Last sync</span>
                    <strong>{{ $lastSync }}</strong>
                </div>
            </div>
            <button type="button" wire:click="$refresh" class="po-sync-btn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>
                Refresh
            </button>
        </div>
    </div>

    {{-- ── Stat cards ── --}}
    <div class="po-cards">

        {{-- Failed payments --}}
        <div class="po-card {{ $failedPayments > 0 ? 'po-danger' : '' }}">
            <div class="po-card-head">
                <div class="po-icon {{ $failedPayments > 0 ? 'po-icon-danger' : 'po-icon-muted' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                </div>
                @if($failedPayments > 0)
                    <span class="po-badge po-badge-danger">Needs attention</span>
                @endif
            </div>
            <strong class="po-num">{{ number_format($failedPayments) }}</strong>
            <div class="po-card-foot">
                <span class="po-label">Failed payments</span>
                <p class="po-desc">Transactions that did not go through</p>
            </div>
        </div>

        {{-- Confirmed no ticket --}}
        <div class="po-card {{ $confirmedNoTicket > 0 ? 'po-warning' : '' }}">
            <div class="po-card-head">
                <div class="po-icon {{ $confirmedNoTicket > 0 ? 'po-icon-warning' : 'po-icon-muted' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 8a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v3a2 2 0 0 0 0 4v3a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-3a2 2 0 0 0 0-4V8z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                </div>
                @if($confirmedNoTicket > 0)
                    <span class="po-badge po-badge-warning">{{ $ticketsPendingIssue }} pending</span>
                @endif
            </div>
            <strong class="po-num">{{ number_format($confirmedNoTicket) }}</strong>
            <div class="po-card-foot">
                <span class="po-label">Confirmed, no ticket</span>
                <p class="po-desc">{{ number_format($ticketsPendingIssue) }} ticket{{ $ticketsPendingIssue == 1 ? '' : 's' }} pending issue</p>
            </div>
        </div>

        {{-- Confirmed today --}}
        <div class="po-card po-success">
            <div class="po-card-head">
                <div class="po-icon po-icon-success">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </div>
                <span class="po-badge po-badge-success">Today</span>
            </div>
            <strong class="po-num">{{ number_format($confirmedToday) }}</strong>
            <div class="po-card-foot">
                <span class="po-label">Confirmed today</span>
                <p class="po-desc">Payments confirmed in the last 24 hours</p>
            </div>
        </div>

        {{-- Events with open issues --}}
        <div class="po-card {{ $eventsWithOpenPayments > 0 ? 'po-warning' : '' }}">
            <div class="po-card-head">
                <div class="po-icon {{ $eventsWithOpenPayments > 0 ? 'po-icon-warning' : 'po-icon-muted' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                </div>
                @if($eventsWithOpenPayments > 0)
                    <span class="po-badge po-badge-warning">Open</span>
                @endif
            </div>
            <strong class="po-num">{{ number_format($eventsWithOpenPayments) }}</strong>
            <div class="po-card-foot">
                <span class="po-label">Events with open issues</span>
                <p class="po-desc">Events with pending or failed payments</p>
            </div>
        </div>

    </div>
</div>

<style>
.po {
    display: grid;
    gap: 0.9rem;
    font-family: var(--wado-admin-font, 'Quicksand', sans-serif);
}

/* ── Filter bar ── */
.po-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    padding: 0.9rem 1.1rem;
    background: #fff;
    border: 1.5px solid #e2e8f0;
    border-radius: 16px;
    box-shadow: 0 1px 4px rgba(15,23,42,.05);
}
.po-bar-left {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    min-width: 0;
}
.po-bar-icon {
    width: 40px; height: 40px;
    border-radius: 12px;
    background: #eff6ff;
    border: 1.5px solid #bfdbfe;
    color: #2563eb;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.po-bar-icon svg { width: 18px; height: 18px; }
.po-loading { font-size: 0.65rem; color: #94a3b8; font-weight: 600; font-style: italic; }
.po-bar-kicker {
    display: block;
    font-size: 0.6rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: #94a3b8;
    line-height: 1;
    margin-bottom: 0.2rem;
}
.po-bar-title {
    display: block;
    font-size: 0.95rem;
    font-weight: 700;
    color: #0f172a;
    line-height: 1.2;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 28ch;
}
.po-bar-right {
    display: flex;
    align-items: flex-end;
    gap: 0.6rem;
    flex-shrink: 0;
}
.po-field { display: grid; gap: 0.22rem; }
.po-field-label {
    font-size: 0.6rem;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: #94a3b8;
}
.po-select-wrap { position: relative; }
.po-select-wrap select {
    -webkit-appearance: none;
    appearance: none;
    height: 40px;
    padding: 0 2.2rem 0 0.85rem;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    background: #f8fafc;
    color: #0f172a;
    font-size: 0.82rem;
    font-weight: 600;
    font-family: inherit;
    cursor: pointer;
    min-width: 210px;
    transition: border-color .15s, box-shadow .15s;
}
.po-select-wrap select:focus {
    outline: none;
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37,99,235,.1);
    background: #fff;
}
.po-chevron {
    position: absolute;
    right: 0.65rem;
    top: 50%;
    transform: translateY(-50%);
    width: 14px; height: 14px;
    color: #94a3b8;
    pointer-events: none;
}
.po-sync-pill {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    height: 40px;
    padding: 0 0.85rem;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    background: #f8fafc;
    white-space: nowrap;
}
.po-sync-pill svg { width: 14px; height: 14px; color: #94a3b8; flex-shrink: 0; }
.po-sync-pill span { display: block; font-size: 0.6rem; font-weight: 700; color: #94a3b8; letter-spacing: .06em; text-transform: uppercase; line-height: 1; }
.po-sync-pill strong { display: block; font-size: 0.8rem; font-weight: 700; color: #334155; line-height: 1.3; }
.po-sync-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    height: 40px;
    padding: 0 1.1rem;
    border: 1.5px solid #2563eb;
    border-radius: 10px;
    background: #2563eb;
    color: #fff;
    font-size: 0.78rem;
    font-weight: 700;
    font-family: inherit;
    cursor: pointer;
    transition: background .15s;
    white-space: nowrap;
}
.po-sync-btn svg { width: 13px; height: 13px; }
.po-sync-btn:hover { background: #1d4ed8; border-color: #1d4ed8; }

/* ── Cards ── */
.po-cards {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 0.65rem;
}
.po-card {
    background: #fff;
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    padding: 0.75rem 0.9rem;
    display: flex;
    flex-direction: column;
    gap: 0.4rem;
    box-shadow: 0 1px 3px rgba(15,23,42,.04);
    transition: box-shadow .15s, transform .15s;
}
.po-card:hover { box-shadow: 0 4px 14px rgba(15,23,42,.08); transform: translateY(-1px); }
.po-danger { background: #fff5f5; border-color: #fca5a5; }
.po-warning { background: #fffbeb; border-color: #fde68a; }
.po-success { background: #f0fdf4; border-color: #bbf7d0; }

.po-card-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.4rem;
}
.po-icon {
    width: 28px; height: 28px;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.po-icon svg { width: 14px; height: 14px; }
.po-icon-muted   { background: #f1f5f9; color: #64748b; }
.po-icon-danger  { background: #fee2e2; color: #dc2626; }
.po-icon-warning { background: #fef3c7; color: #d97706; }
.po-icon-success { background: #dcfce7; color: #16a34a; }

.po-badge {
    display: inline-flex;
    align-items: center;
    border-radius: 999px;
    padding: 0.14rem 0.45rem;
    font-size: 0.58rem;
    font-weight: 700;
    line-height: 1;
    border: 1px solid;
}
.po-badge-danger  { background: #fee2e2; color: #b91c1c; border-color: #fca5a5; }
.po-badge-warning { background: #fef3c7; color: #92400e; border-color: #fde68a; }
.po-badge-success { background: #dcfce7; color: #166534; border-color: #bbf7d0; }

.po-num {
    font-size: 1.65rem;
    font-weight: 800;
    color: #0f172a;
    line-height: 1;
    letter-spacing: -0.02em;
}
.po-danger  .po-num { color: #7f1d1d; }
.po-warning .po-num { color: #78350f; }
.po-success .po-num { color: #14532d; }

.po-card-foot { display: grid; gap: 0.1rem; }
.po-label {
    font-size: 0.74rem;
    font-weight: 700;
    color: #374151;
}
.po-danger  .po-label { color: #991b1b; }
.po-warning .po-label { color: #92400e; }
.po-success .po-label { color: #166534; }

.po-desc {
    margin: 0;
    font-size: 0.66rem;
    color: #94a3b8;
    line-height: 1.3;
}
.po-danger  .po-desc { color: #ef4444; }
.po-warning .po-desc { color: #d97706; }
.po-success .po-desc { color: #4ade80; }

@media (max-width: 1180px) {
    .po-bar { flex-direction: column; align-items: stretch; }
    .po-bar-right { flex-wrap: wrap; }
    .po-select-wrap select { min-width: 0; width: 100%; }
    .po-cards { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 640px) {
    .po-cards { grid-template-columns: 1fr; }
    .po-bar-right { flex-direction: column; }
    .po-sync-btn { justify-content: center; }
}
</style>
</x-filament-widgets::widget>
