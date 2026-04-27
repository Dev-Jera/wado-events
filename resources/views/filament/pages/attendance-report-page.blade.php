<x-filament-panels::page>

<style>
    .att-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }
    .att-card {
        background: #fff;
        border-radius: 12px;
        padding: 1.1rem 1.4rem;
        box-shadow: 0 1px 4px rgba(0,0,0,.08);
        border: 1px solid #f0f4fb;
    }
    .att-card-label {
        font-size: .72rem;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: #6b7280;
        font-weight: 700;
    }
    .att-card-value {
        font-size: 1.6rem;
        font-weight: 800;
        margin-top: .25rem;
        font-variant-numeric: tabular-nums;
        color: #1e293b;
    }
    .att-card-value.c-blue  { color: #1d4ed8; }
    .att-card-value.c-green { color: #16a34a; }

    .att-table {
        width: 100%;
        border-collapse: collapse;
        background: #fff;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 1px 4px rgba(0,0,0,.08);
        border: 1px solid #f0f4fb;
    }
    .att-table th {
        background: #1a3a8f;
        color: #e2e8f0;
        padding: .65rem 1rem;
        text-align: left;
        font-size: .75rem;
        text-transform: uppercase;
        letter-spacing: .06em;
        white-space: nowrap;
    }
    .att-table th.num { text-align: right; }
    .att-table td {
        padding: .7rem 1rem;
        border-bottom: 1px solid #f1f5f9;
        font-size: .85rem;
        color: #374151;
    }
    .att-table tr:last-child td { border-bottom: none; }
    .att-table td.num { text-align: right; font-variant-numeric: tabular-nums; }
    .att-table tfoot td {
        background: #f8fafc;
        font-weight: 700;
        color: #1e293b;
        border-top: 2px solid #e2e8f0;
    }

    .att-status {
        display: inline-block;
        font-size: .7rem;
        font-weight: 700;
        padding: .18rem .55rem;
        border-radius: 999px;
        text-transform: capitalize;
        letter-spacing: .03em;
    }
    .att-status.live     { background: #dcfce7; color: #166534; }
    .att-status.upcoming { background: #dbeafe; color: #1e40af; }
    .att-status.ended    { background: #f1f5f9; color: #475569; }
    .att-status.draft    { background: #fef9c3; color: #854d0e; }
    .att-status.cancelled{ background: #fee2e2; color: #991b1b; }

    .att-bar-wrap { width: 90px; display: inline-block; vertical-align: middle; margin-left: .4rem; }
    .att-bar-bg   { height: 5px; background: #e2e8f0; border-radius: 3px; }
    .att-bar-fill { height: 5px; background: #2563eb; border-radius: 3px; }

    .att-rate { font-weight: 700; color: #2563eb; }
    .att-rate.high  { color: #16a34a; }
    .att-rate.low   { color: #dc2626; }

    .att-empty {
        text-align: center;
        padding: 3rem 1rem;
        color: #94a3b8;
        font-size: .9rem;
    }
</style>

{{-- ── Summary cards ─────────────────────────────── --}}
<div class="att-cards">
    <div class="att-card">
        <div class="att-card-label">Total Capacity</div>
        <div class="att-card-value">{{ number_format($totals['capacity']) }}</div>
    </div>
    <div class="att-card">
        <div class="att-card-label">Tickets Sold</div>
        <div class="att-card-value c-blue">{{ number_format($totals['sold']) }}</div>
    </div>
    <div class="att-card">
        <div class="att-card-label">Scanned In</div>
        <div class="att-card-value c-green">{{ number_format($totals['scanned']) }}</div>
    </div>
    <div class="att-card">
        <div class="att-card-label">Overall Attendance</div>
        <div class="att-card-value {{ $totals['rate'] >= 70 ? 'c-green' : '' }}">
            {{ $totals['rate'] }}%
        </div>
    </div>
</div>

{{-- ── Per-event table ─────────────────────────────── --}}
@if ($events->isEmpty())
    <div class="att-empty">No events found.</div>
@else
<table class="att-table">
    <thead>
        <tr>
            <th>Event</th>
            <th>Date</th>
            <th>Status</th>
            <th class="num">Capacity</th>
            <th class="num">Sold</th>
            <th class="num">Fill Rate</th>
            <th class="num">Scanned In</th>
            <th class="num">Attendance</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($events as $event)
        @php
            $rateClass = $event['attendance_rate'] >= 70 ? 'high' : ($event['attendance_rate'] <= 30 ? 'low' : '');
        @endphp
        <tr>
            <td style="max-width:220px;">{{ \Illuminate\Support\Str::limit($event['title'], 40) }}</td>
            <td style="white-space:nowrap;">{{ $event['date'] }}</td>
            <td>
                <span class="att-status {{ $event['status'] }}">{{ $event['status'] }}</span>
            </td>
            <td class="num">{{ number_format($event['capacity']) }}</td>
            <td class="num">{{ number_format($event['sold']) }}</td>
            <td class="num">
                {{ $event['fill_rate'] }}%
                <span class="att-bar-wrap">
                    <div class="att-bar-bg">
                        <div class="att-bar-fill" style="width:{{ min($event['fill_rate'], 100) }}%;"></div>
                    </div>
                </span>
            </td>
            <td class="num">{{ number_format($event['scanned']) }}</td>
            <td class="num">
                <span class="att-rate {{ $rateClass }}">{{ $event['attendance_rate'] }}%</span>
            </td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="3">All Events</td>
            <td class="num">{{ number_format($totals['capacity']) }}</td>
            <td class="num">{{ number_format($totals['sold']) }}</td>
            <td class="num">—</td>
            <td class="num">{{ number_format($totals['scanned']) }}</td>
            <td class="num">{{ $totals['rate'] }}%</td>
        </tr>
    </tfoot>
</table>
@endif

</x-filament-panels::page>
