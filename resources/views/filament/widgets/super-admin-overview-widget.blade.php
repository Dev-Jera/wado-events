<x-filament-widgets::widget>
    <div class="pc-board">
        <section class="pc-hero">
            <div class="pc-hero-main">
                <p class="pc-kicker">{{ strtoupper($dashboardTitle) }}</p>
                <h2>UGX {{ number_format($revenueTotal, 0) }}</h2>
                <p class="pc-sub">{{ $dashboardSubtitle }}</p>
            </div>
            <div class="pc-hero-side">
                <div class="pc-hero-chip">
                    <span>Revenue this month</span>
                    <strong>UGX {{ number_format($revenueThisMonth, 0) }}</strong>
                </div>
                <div class="pc-hero-chip">
                    <span>Tickets sold this month</span>
                    <strong>{{ number_format($ticketsSoldThisMonth) }}</strong>
                </div>
            </div>
        </section>

        <section class="pc-stat-grid">
            <article class="pc-stat-card">
                <span>Pending Payments</span>
                <strong>{{ number_format($pendingPayments) }}</strong>
                <small>Awaiting provider confirmation</small>
            </article>
            <article class="pc-stat-card">
                <span>Failed Payments</span>
                <strong>{{ number_format($failedPayments) }}</strong>
                <small>Need support follow-up</small>
            </article>
            <article class="pc-stat-card">
                <span>Unissued QR</span>
                <strong>{{ number_format($qrNotIssued) }}</strong>
                <small>Confirmed without generated QR</small>
            </article>
            <article class="pc-stat-card">
                <span>Gate Remaining</span>
                <strong>{{ number_format($atGateUnscanned) }}</strong>
                <small>Issued but not scanned</small>
            </article>
            <article class="pc-stat-card">
                <span>{{ $opsMetricLabel }}</span>
                <strong>{{ number_format($opsMetricValue) }}</strong>
                <small>{{ $isEventOwner ? 'Events currently running' : 'Assigned gate agents' }}</small>
            </article>
        </section>

        <section class="pc-main-grid">
            <article class="pc-panel">
                <div class="pc-panel-head">
                    <h3>Recent Payments</h3>
                    <a href="{{ route('filament.admin.resources.payment-transactions.index') }}">View all</a>
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
                                    <td>{{ $payment['holder'] }}</td>
                                    <td>{{ $payment['event'] }}</td>
                                    <td class="mono">{{ $payment['phone'] }}</td>
                                    <td class="num">{{ $payment['currency'] }} {{ number_format($payment['amount'], 0) }}</td>
                                    <td><span class="pc-badge pc-badge-{{ $payment['status'] }}">{{ ucfirst($payment['status']) }}</span></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="empty">No payment activity yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </article>

            <article class="pc-panel">
                <div class="pc-panel-head">
                    <h3>Event Health</h3>
                </div>
                <div class="pc-event-list">
                    @forelse ($events as $event)
                        @php
                            $sellRate = $event['capacity'] > 0 ? round(($event['sold'] / $event['capacity']) * 100) : 0;
                        @endphp
                        <div class="pc-event-row">
                            <div class="pc-event-head">
                                <strong>{{ \Illuminate\Support\Str::limit($event['title'], 28) }}</strong>
                                <span>{{ $sellRate }}%</span>
                            </div>
                            <div class="pc-progress">
                                <span style="width: {{ min($sellRate, 100) }}%"></span>
                            </div>
                            <div class="pc-event-meta">
                                <small>{{ number_format($event['sold']) }}/{{ number_format($event['capacity']) }} sold</small>
                                <small>UGX {{ number_format($event['revenue'], 0) }}</small>
                            </div>
                        </div>
                    @empty
                        <p class="empty">No events found.</p>
                    @endforelse
                </div>
            </article>
        </section>

        <section class="pc-panel">
            <div class="pc-panel-head">
                <h3>6-Month Ticket Activity</h3>
                <p class="pc-inline-note">Issued vs scanned</p>
            </div>
            <div class="pc-chart-wrap">
                @foreach ($chartData as $bar)
                    @php
                        $issuedPct = $maxBarValue > 0 ? round(($bar['issued'] / $maxBarValue) * 100) : 0;
                        $scannedPct = $maxBarValue > 0 ? round(($bar['scanned'] / $maxBarValue) * 100) : 0;
                    @endphp
                    <div class="pc-chart-col">
                        <div class="pc-chart-bars">
                            <span class="issued" style="height: max({{ $issuedPct }}%, 4px)"></span>
                            <span class="scanned" style="height: max({{ $scannedPct }}%, 4px)"></span>
                        </div>
                        <small>{{ $bar['month'] }}</small>
                    </div>
                @endforeach
            </div>
        </section>
    </div>

    <style>
        .pc-board {
            display: grid;
            gap: 0.9rem;
            font-family: var(--wado-admin-font, 'Quicksand', 'Nunito', sans-serif);
        }

        .pc-hero {
            display: grid;
            grid-template-columns: 1.4fr 1fr;
            gap: 0.9rem;
            border-radius: 14px;
            border: 1px solid #dbe4f0;
            background: linear-gradient(130deg, #0d2b7a 0%, #0f3fa2 60%, #2256b8 100%);
            padding: 1rem;
            color: #fff;
        }

        .pc-kicker {
            margin: 0;
            font-size: 0.7rem;
            letter-spacing: 0.02em;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.78);
        }

        .pc-hero-main h2 {
            margin: 0.22rem 0 0;
            font-size: clamp(1.35rem, 2.4vw, 1.95rem);
            line-height: 1.05;
            font-weight: 700;
            letter-spacing: 0;
        }

        .pc-sub {
            margin: 0.45rem 0 0;
            font-size: 0.79rem;
            color: rgba(255, 255, 255, 0.84);
            max-width: 54ch;
        }

        .pc-hero-side {
            display: grid;
            gap: 0.55rem;
        }

        .pc-hero-chip {
            border: 1px solid rgba(255, 255, 255, 0.24);
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 0.65rem 0.75rem;
            display: grid;
            gap: 0.2rem;
        }

        .pc-hero-chip span {
            font-size: 0.68rem;
            letter-spacing: 0.01em;
            text-transform: none;
            color: rgba(255, 255, 255, 0.74);
            font-weight: 600;
        }

        .pc-hero-chip strong {
            font-size: 0.98rem;
            line-height: 1.1;
            font-weight: 700;
        }

        .pc-stat-grid {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 0.7rem;
        }

        .pc-stat-card {
            background: #ffffff;
            border: 1px solid #dbe4f0;
            border-radius: 12px;
            padding: 0.72rem 0.78rem;
            display: grid;
            gap: 0.22rem;
        }

        .pc-stat-card span {
            font-size: 0.69rem;
            color: #51637f;
            letter-spacing: 0.02em;
            font-weight: 600;
        }

        .pc-stat-card strong {
            color: #12233d;
            font-size: 1.22rem;
            line-height: 1;
            font-weight: 700;
        }

        .pc-stat-card small {
            color: #7d8da7;
            font-size: 0.66rem;
            line-height: 1.3;
        }

        .pc-main-grid {
            display: grid;
            grid-template-columns: 1.5fr 1fr;
            gap: 0.7rem;
        }

        .pc-panel {
            background: #ffffff;
            border: 1px solid #dbe4f0;
            border-radius: 12px;
            overflow: hidden;
        }

        .pc-panel-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.7rem;
            border-bottom: 1px solid #e8eef7;
            padding: 0.72rem 0.82rem;
        }

        .pc-panel-head h3 {
            margin: 0;
            font-size: 0.83rem;
            color: #12233d;
            font-weight: 700;
        }

        .pc-panel-head a {
            text-decoration: none;
            color: #1b4fb0;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .pc-inline-note {
            margin: 0;
            font-size: 0.66rem;
            color: #6d7f9e;
            font-weight: 600;
        }

        .pc-table-wrap { overflow: auto; }

        .pc-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 560px;
        }

        .pc-table th,
        .pc-table td {
            padding: 0.56rem 0.7rem;
            border-bottom: 1px solid #edf2f9;
            text-align: left;
            color: #243a5c;
            font-size: 0.71rem;
        }

        .pc-table th {
            background: #f7faff;
            color: #5f7597;
            font-size: 0.66rem;
            text-transform: none;
            letter-spacing: 0.01em;
            font-weight: 600;
        }

        .pc-table .num { text-align: right; }
        .pc-table .mono { font-family: ui-monospace, SFMono-Regular, Menlo, monospace; }
        .pc-table .empty {
            text-align: center;
            color: #7d8da7;
            padding: 1rem;
        }

        .pc-badge {
            display: inline-flex;
            border-radius: 999px;
            border: 1px solid;
            padding: 0.16rem 0.44rem;
            font-size: 0.66rem;
            font-weight: 600;
            line-height: 1;
        }

        .pc-badge-initiated, .pc-badge-pending { background: #edf3ff; color: #1f4c96; border-color: #cfdcf8; }
        .pc-badge-confirmed { background: #ebfbf1; color: #16643f; border-color: #bde8cb; }
        .pc-badge-failed { background: #fff2f4; color: #9e2135; border-color: #f4cad1; }
        .pc-badge-refunded { background: #fff7eb; color: #92520a; border-color: #ffdfba; }

        .pc-event-list {
            display: grid;
            gap: 0.58rem;
            padding: 0.68rem 0.78rem 0.8rem;
        }

        .pc-event-row {
            border: 1px solid #e8eef7;
            border-radius: 10px;
            padding: 0.55rem 0.62rem;
            display: grid;
            gap: 0.36rem;
        }

        .pc-event-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 0.5rem;
        }

        .pc-event-head strong {
            color: #1a2f4e;
            font-size: 0.73rem;
            font-weight: 700;
        }

        .pc-event-head span {
            color: #1b4fb0;
            font-size: 0.69rem;
            font-weight: 700;
        }

        .pc-progress {
            width: 100%;
            height: 6px;
            border-radius: 999px;
            background: #e9f0fb;
            overflow: hidden;
        }

        .pc-progress span {
            display: block;
            height: 100%;
            border-radius: inherit;
            background: linear-gradient(90deg, #2b66cc 0%, #1f4da8 100%);
        }

        .pc-event-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.5rem;
        }

        .pc-event-meta small {
            color: #6f82a2;
            font-size: 0.64rem;
            font-weight: 600;
        }

        .pc-chart-wrap {
            display: flex;
            align-items: end;
            justify-content: space-between;
            gap: 0.45rem;
            padding: 0.8rem 0.8rem 0.9rem;
            min-height: 146px;
        }

        .pc-chart-col {
            display: grid;
            gap: 0.36rem;
            justify-items: center;
            flex: 1;
        }

        .pc-chart-bars {
            width: 100%;
            height: 96px;
            display: flex;
            justify-content: center;
            align-items: end;
            gap: 4px;
        }

        .pc-chart-bars span {
            width: min(14px, 38%);
            border-radius: 6px 6px 2px 2px;
            display: block;
        }

        .pc-chart-bars .issued { background: #1f5ec6; }
        .pc-chart-bars .scanned { background: #4ab39a; }

        .pc-chart-col small {
            color: #6b7e9d;
            font-size: 0.62rem;
            font-weight: 600;
        }

        .empty {
            margin: 0;
            color: #7d8da7;
            font-size: 0.72rem;
            padding: 0.8rem;
            text-align: center;
        }

        @media (max-width: 1200px) {
            .pc-stat-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
            .pc-main-grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 760px) {
            .pc-hero { grid-template-columns: 1fr; }
            .pc-stat-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }

        @media (max-width: 520px) {
            .pc-stat-grid { grid-template-columns: 1fr; }
        }
    </style>
</x-filament-widgets::widget>
