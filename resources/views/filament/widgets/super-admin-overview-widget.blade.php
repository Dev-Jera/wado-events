<x-filament-widgets::widget>
@php
    $trendUp   = $revenuePctChange >= 0;
    $trendIcon = $trendUp ? '↑' : '↓';
    $trendClass = $trendUp ? 'pc-trend-up' : 'pc-trend-down';
@endphp
<div class="pc">

    {{-- ── Hero ── --}}
    <section class="pc-hero">
        <div class="pc-hero-left">
            <p class="pc-kicker">{{ strtoupper($dashboardTitle) }}</p>
            <div class="pc-revenue-row">
                <h2>UGX {{ number_format($revenueTotal, 0) }}</h2>
                <span class="pc-trend {{ $trendClass }}">
                    {{ $trendIcon }} {{ abs($revenuePctChange) }}%
                </span>
            </div>
            <p class="pc-sub">{{ $dashboardSubtitle }}</p>
        </div>
        <div class="pc-hero-chips">
            <div class="pc-chip">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                <div><span>Total events</span><strong>{{ number_format($totalEvents) }}</strong></div>
            </div>
            <div class="pc-chip">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 8a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v3a2 2 0 0 0 0 4v3a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-3a2 2 0 0 0 0-4V8z"/></svg>
                <div><span>Tickets sold</span><strong>{{ number_format($ticketsSoldTotal) }}</strong></div>
            </div>
            <div class="pc-chip">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                <div><span>{{ $opsMetricLabel }}</span><strong>{{ number_format($opsMetricValue) }}</strong></div>
            </div>
            <div class="pc-chip">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
                <div><span>This month</span><strong>UGX {{ number_format($revenueThisMonth, 0) }}</strong></div>
            </div>
            <div class="pc-chip">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                <div><span>New events</span><strong>{{ number_format($newEventsThisMonth) }}</strong></div>
            </div>
            <div class="pc-chip">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                <div><span>Tickets / month</span><strong>{{ number_format($ticketsSoldThisMonth) }}</strong></div>
            </div>
        </div>
    </section>

    {{-- ── Main grid ── --}}
    <div class="pc-grid">

        {{-- Recent Payments --}}
        <section class="pc-panel">
            <div class="pc-panel-head">
                <div class="pc-panel-title">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                    <h3>Recent Payments</h3>
                </div>
                <a href="{{ route('filament.admin.resources.payment-transactions.index') }}" class="pc-view-all">View all →</a>
            </div>
            <div class="pc-table-wrap">
                <table class="pc-table">
                    <thead>
                        <tr>
                            <th>Holder</th>
                            <th>Event</th>
                            <th>Phone</th>
                            <th class="num">Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentPayments as $payment)
                            <tr>
                                <td><strong>{{ $payment['holder'] }}</strong></td>
                                <td class="muted">{{ $payment['event'] }}</td>
                                <td class="mono muted">{{ $payment['phone'] }}</td>
                                <td class="num bold">{{ $payment['currency'] }} {{ number_format($payment['amount'], 0) }}</td>
                                <td><span class="pc-badge pc-badge-{{ $payment['status'] }}">{{ ucfirst($payment['status']) }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="pc-empty">No payment activity yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        {{-- Event Health --}}
        <section class="pc-panel">
            <div class="pc-panel-head">
                <div class="pc-panel-title">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                    <h3>Event Health</h3>
                </div>
            </div>
            <div class="pc-events">
                @forelse ($events as $event)
                    @php $pct = $event['capacity'] > 0 ? round(($event['sold'] / $event['capacity']) * 100) : 0; @endphp
                    <div class="pc-event">
                        <div class="pc-event-row">
                            <strong>{{ \Illuminate\Support\Str::limit($event['title'], 26) }}</strong>
                            <span class="pc-pct {{ $pct >= 80 ? 'pc-pct-hot' : '' }}">{{ $pct }}%</span>
                        </div>
                        <div class="pc-bar">
                            <div class="pc-bar-fill {{ $pct >= 80 ? 'pc-bar-hot' : '' }}" style="width:{{ min($pct,100) }}%"></div>
                        </div>
                        <div class="pc-event-meta">
                            <small>{{ number_format($event['sold']) }} / {{ number_format($event['capacity']) }} sold</small>
                            <small class="bold-muted">UGX {{ number_format($event['revenue'], 0) }}</small>
                        </div>
                    </div>
                @empty
                    <p class="pc-empty">No events found.</p>
                @endforelse
            </div>
        </section>
    </div>

    {{-- ── Chart ── --}}
    <section class="pc-panel">
        <div class="pc-panel-head">
            <div class="pc-panel-title">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="18" y="3" width="4" height="18"/><rect x="10" y="8" width="4" height="13"/><rect x="2" y="13" width="4" height="8"/></svg>
                <h3>6-Month Ticket Activity</h3>
            </div>
            <div class="pc-legend">
                <span class="pc-legend-dot" style="background:#2563eb"></span><span>Issued</span>
                <span class="pc-legend-dot" style="background:#34d399"></span><span>Scanned</span>
            </div>
        </div>
        <div class="pc-chart">
            @foreach ($chartData as $bar)
                @php
                    $ip = $maxBarValue > 0 ? round(($bar['issued']  / $maxBarValue) * 100) : 0;
                    $sp = $maxBarValue > 0 ? round(($bar['scanned'] / $maxBarValue) * 100) : 0;
                @endphp
                <div class="pc-col">
                    <div class="pc-bars">
                        <div class="pc-bar-wrap">
                            <span class="pc-bar-issued"  style="height:max({{ $ip }}%,4px)"></span>
                        </div>
                        <div class="pc-bar-wrap">
                            <span class="pc-bar-scanned" style="height:max({{ $sp }}%,4px)"></span>
                        </div>
                    </div>
                    <small>{{ $bar['month'] }}</small>
                </div>
            @endforeach
        </div>
    </section>

</div>

<style>
.pc {
    display: grid;
    gap: 0.9rem;
    font-family: var(--wado-admin-font, 'Quicksand', sans-serif);
}

/* ── Hero ── */
.pc-hero {
    display: grid;
    grid-template-columns: 1fr 1.4fr;
    gap: 1.2rem;
    background: linear-gradient(130deg, #1d4ed8 0%, #2563eb 55%, #3b82f6 100%);
    border-radius: 18px;
    padding: 1.4rem 1.5rem;
    color: #fff;
    border: 1px solid #93c5fd;
    box-shadow: 0 8px 32px rgba(37,99,235,.25);
}
.pc-kicker {
    margin: 0 0 0.3rem;
    font-size: 0.6rem;
    font-weight: 700;
    letter-spacing: 0.12em;
    color: rgba(255,255,255,.6);
    text-transform: uppercase;
}
.pc-revenue-row {
    display: flex;
    align-items: baseline;
    gap: 0.75rem;
    flex-wrap: wrap;
}
.pc-hero-left h2 {
    margin: 0;
    font-size: clamp(1.5rem, 2.5vw, 2.2rem);
    font-weight: 800;
    letter-spacing: -0.02em;
    line-height: 1;
}
.pc-trend {
    display: inline-flex;
    align-items: center;
    padding: 0.22rem 0.6rem;
    border-radius: 999px;
    font-size: 0.72rem;
    font-weight: 700;
    line-height: 1;
}
.pc-trend-up   { background: rgba(52,211,153,.25); color: #6ee7b7; border: 1px solid rgba(52,211,153,.3); }
.pc-trend-down { background: rgba(248,113,113,.2); color: #fca5a5; border: 1px solid rgba(248,113,113,.3); }
.pc-sub {
    margin: 0.55rem 0 0;
    font-size: 0.8rem;
    color: rgba(255,255,255,.7);
    line-height: 1.4;
    max-width: 42ch;
}

/* ── Hero chips ── */
.pc-hero-chips {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 0.5rem;
    align-content: center;
}
.pc-chip {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(255,255,255,.12);
    border: 1px solid rgba(255,255,255,.18);
    border-radius: 12px;
    padding: 0.6rem 0.7rem;
    backdrop-filter: blur(4px);
}
.pc-chip svg {
    width: 15px; height: 15px;
    color: rgba(255,255,255,.7);
    flex-shrink: 0;
}
.pc-chip span {
    display: block;
    font-size: 0.6rem;
    font-weight: 700;
    color: rgba(255,255,255,.55);
    text-transform: uppercase;
    letter-spacing: 0.06em;
    line-height: 1;
    margin-bottom: 0.18rem;
}
.pc-chip strong {
    display: block;
    font-size: 0.88rem;
    font-weight: 700;
    color: #fff;
    line-height: 1;
}

/* ── Main grid ── */
.pc-grid {
    display: grid;
    grid-template-columns: 1.5fr 1fr;
    gap: 0.9rem;
}

/* ── Panel ── */
.pc-panel {
    background: #fff;
    border: 1.5px solid #e2e8f0;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 1px 4px rgba(15,23,42,.04);
}
.pc-panel-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
    padding: 0.85rem 1rem;
    border-bottom: 1px solid #f1f5f9;
    background: #fafbfd;
}
.pc-panel-title {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.pc-panel-title svg { width: 16px; height: 16px; color: #2563eb; }
.pc-panel-title h3 {
    margin: 0;
    font-size: 0.86rem;
    font-weight: 700;
    color: #0f172a;
}
.pc-view-all {
    text-decoration: none;
    color: #2563eb;
    font-size: 0.72rem;
    font-weight: 700;
    transition: opacity .15s;
}
.pc-view-all:hover { opacity: 0.75; }

/* ── Table ── */
.pc-table-wrap { overflow-x: auto; }
.pc-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 520px;
}
.pc-table th {
    padding: 0.55rem 0.85rem;
    background: #f8fafc;
    color: #64748b;
    font-size: 0.65rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    text-align: left;
    border-bottom: 1px solid #f1f5f9;
}
.pc-table td {
    padding: 0.7rem 0.85rem;
    border-bottom: 1px solid #f8fafc;
    font-size: 0.75rem;
    color: #334155;
}
.pc-table tr:last-child td { border-bottom: none; }
.pc-table tr:hover td { background: #fafbff; }
.pc-table .num { text-align: right; }
.pc-table .mono { font-family: ui-monospace, monospace; }
.pc-table .muted { color: #64748b; }
.pc-table .bold { font-weight: 700; color: #0f172a; }
.pc-empty {
    text-align: center;
    color: #94a3b8;
    padding: 1.5rem;
    font-size: 0.78rem;
}

/* ── Badges ── */
.pc-badge {
    display: inline-flex;
    border-radius: 999px;
    padding: 0.18rem 0.55rem;
    font-size: 0.65rem;
    font-weight: 700;
    line-height: 1;
    border: 1px solid;
}
.pc-badge-initiated, .pc-badge-pending { background:#eef4ff; color:#1d4ed8; border-color:#bfdbfe; }
.pc-badge-confirmed { background:#f0fdf4; color:#16a34a; border-color:#bbf7d0; }
.pc-badge-failed    { background:#fff5f5; color:#dc2626; border-color:#fca5a5; }
.pc-badge-refunded  { background:#fffbeb; color:#d97706; border-color:#fde68a; }

/* ── Event health ── */
.pc-events { display: grid; gap: 0; }
.pc-event {
    display: grid;
    gap: 0.38rem;
    padding: 0.8rem 1rem;
    border-bottom: 1px solid #f8fafc;
}
.pc-event:last-child { border-bottom: none; }
.pc-event-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.5rem;
}
.pc-event-row strong { font-size: 0.78rem; font-weight: 700; color: #0f172a; }
.pc-pct { font-size: 0.72rem; font-weight: 700; color: #2563eb; }
.pc-pct-hot { color: #dc2626; }
.pc-bar {
    width: 100%;
    height: 6px;
    border-radius: 999px;
    background: #f1f5f9;
    overflow: hidden;
}
.pc-bar-fill {
    height: 100%;
    border-radius: inherit;
    background: linear-gradient(90deg, #2563eb, #60a5fa);
    transition: width .4s ease;
}
.pc-bar-hot { background: linear-gradient(90deg, #dc2626, #f87171); }
.pc-event-meta {
    display: flex;
    justify-content: space-between;
    gap: 0.5rem;
}
.pc-event-meta small { font-size: 0.67rem; color: #94a3b8; font-weight: 600; }
.bold-muted { font-weight: 700 !important; color: #475569 !important; }

/* ── Chart ── */
.pc-legend {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.68rem;
    font-weight: 600;
    color: #64748b;
}
.pc-legend-dot {
    width: 8px; height: 8px;
    border-radius: 50%;
    display: inline-block;
}
.pc-chart {
    display: flex;
    align-items: flex-end;
    gap: 0.5rem;
    padding: 1rem 1.2rem 1.1rem;
    min-height: 140px;
}
.pc-col {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.4rem;
    flex: 1;
}
.pc-col small { font-size: 0.64rem; color: #94a3b8; font-weight: 700; }
.pc-bars {
    width: 100%;
    height: 90px;
    display: flex;
    align-items: flex-end;
    justify-content: center;
    gap: 3px;
}
.pc-bar-wrap {
    flex: 1;
    height: 100%;
    display: flex;
    align-items: flex-end;
}
.pc-bar-issued, .pc-bar-scanned {
    display: block;
    width: 100%;
    border-radius: 5px 5px 2px 2px;
}
.pc-bar-issued  { background: #2563eb; }
.pc-bar-scanned { background: #34d399; }

/* ── Responsive ── */
@media (max-width: 1200px) {
    .pc-grid { grid-template-columns: 1fr; }
}
@media (max-width: 900px) {
    .pc-hero { grid-template-columns: 1fr; }
    .pc-hero-chips { grid-template-columns: repeat(3, 1fr); }
}
@media (max-width: 560px) {
    .pc-hero { padding: 1rem; }
    .pc-hero-chips { grid-template-columns: repeat(2, 1fr); }
    .pc-chart { overflow-x: auto; justify-content: flex-start; }
    .pc-col { min-width: 40px; }
}
</style>
</x-filament-widgets::widget>
