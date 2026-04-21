@extends('layouts.admin')

@section('title', 'Finance — ' . $event->title)
@section('heading', $event->title)

@section('content')
<style>
    .fin-back { color: #b45309; font-weight: 700; text-decoration: none; font-size: .9rem; display: inline-block; margin-bottom: 1.2rem; }
    .fin-back:hover { text-decoration: underline; }
    .fin-export { float: right; background: #08111f; color: #fff; padding: .5rem 1.1rem; border-radius: 8px; text-decoration: none; font-size: .85rem; font-weight: 700; }
    .fin-export:hover { background: #0f1b31; }
    .fin-cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
    .fin-card { background: #fff; border-radius: 14px; padding: 1.25rem 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,.07); }
    .fin-card-label { font-size: .75rem; text-transform: uppercase; letter-spacing: .08em; color: #6b7280; font-weight: 700; }
    .fin-card-value { font-size: 1.7rem; font-weight: 800; margin-top: .3rem; font-variant-numeric: tabular-nums; }
    .c-green { color: #16a34a; }
    .c-red { color: #dc2626; }
    .c-amber { color: #d97706; }
    .c-blue { color: #1d4ed8; }
    .fin-section { margin-bottom: 2rem; }
    .fin-section h2 { font-size: 1rem; text-transform: uppercase; letter-spacing: .08em; color: #6b7280; margin: 0 0 .75rem; }
    .fin-table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 14px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,.07); }
    .fin-table th { background: #08111f; color: #d7e1f2; padding: .65rem 1rem; text-align: left; font-size: .78rem; text-transform: uppercase; letter-spacing: .06em; }
    .fin-table td { padding: .7rem 1rem; border-bottom: 1px solid #f0f4fb; font-size: .9rem; }
    .fin-table tr:last-child td { border-bottom: none; }
    .amount { font-weight: 700; font-variant-numeric: tabular-nums; }
    .provider-badge { display: inline-block; padding: .2rem .65rem; border-radius: 999px; font-size: .75rem; font-weight: 800; text-transform: uppercase; }
    .p-mtn { background: #fef9c3; color: #854d0e; }
    .p-airtel { background: #fee2e2; color: #991b1b; }
    .p-cash { background: #dcfce7; color: #166534; }
    .p-pos { background: #dbeafe; color: #1e40af; }
    .p-free { background: #f3f4f6; color: #374151; }
    .p-default { background: #f3f4f6; color: #374151; }
</style>

<a href="{{ route('admin.finance.index') }}" class="fin-back">← All Events</a>
<a href="{{ route('admin.finance.show', ['event' => $event, 'export' => 1]) }}" class="fin-export">Export CSV</a>

<div style="clear:both;margin-bottom:1.5rem;">
    <span style="color:#6b7280;font-size:.9rem;">{{ $event->starts_at?->format('d M Y, H:i') }} · {{ $event->venue }}, {{ $event->city }}</span>
</div>

{{-- Summary cards --}}
<div class="fin-cards">
    <div class="fin-card">
        <div class="fin-card-label">Collected</div>
        <div class="fin-card-value c-green">{{ number_format((float) ($summary->confirmed ?? 0), 0) }}</div>
        <div style="font-size:.75rem;color:#6b7280;margin-top:.2rem;">UGX</div>
    </div>
    <div class="fin-card">
        <div class="fin-card-label">Refunded</div>
        <div class="fin-card-value c-red">{{ number_format((float) ($summary->refunded ?? 0), 0) }}</div>
        <div style="font-size:.75rem;color:#6b7280;margin-top:.2rem;">UGX</div>
    </div>
    <div class="fin-card">
        <div class="fin-card-label">Net Revenue</div>
        <div class="fin-card-value c-blue">{{ number_format(((float)($summary->confirmed ?? 0)) - ((float)($summary->refunded ?? 0)), 0) }}</div>
        <div style="font-size:.75rem;color:#6b7280;margin-top:.2rem;">UGX</div>
    </div>
    <div class="fin-card">
        <div class="fin-card-label">Pending</div>
        <div class="fin-card-value c-amber">{{ number_format((float) ($summary->pending ?? 0), 0) }}</div>
        <div style="font-size:.75rem;color:#6b7280;margin-top:.2rem;">UGX</div>
    </div>
    <div class="fin-card">
        <div class="fin-card-label">Tickets Sold</div>
        <div class="fin-card-value">{{ (int) ($summary->tickets_sold ?? 0) }}</div>
    </div>
    <div class="fin-card">
        <div class="fin-card-label">Tickets Refunded</div>
        <div class="fin-card-value c-red">{{ (int) ($summary->tickets_refunded ?? 0) }}</div>
    </div>
</div>

{{-- By payment channel --}}
<div class="fin-section">
    <h2>By Payment Channel</h2>
    <table class="fin-table">
        <thead>
            <tr>
                <th>Channel</th>
                <th>Tickets</th>
                <th>Amount Collected (UGX)</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($byChannel as $row)
                @php
                    $provider = strtolower((string) $row->payment_provider);
                    $badgeClass = match($provider) {
                        'mtn'    => 'p-mtn',
                        'airtel' => 'p-airtel',
                        'cash'   => 'p-cash',
                        'pos'    => 'p-pos',
                        'free'   => 'p-free',
                        default  => 'p-default',
                    };
                @endphp
                <tr>
                    <td><span class="provider-badge {{ $badgeClass }}">{{ strtoupper($provider) }}</span></td>
                    <td>{{ (int) $row->tickets }}</td>
                    <td class="amount">{{ number_format((float) $row->total, 0) }}</td>
                </tr>
            @empty
                <tr><td colspan="3" style="color:#6b7280;text-align:center;padding:1.5rem;">No confirmed payments yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- By ticket category --}}
<div class="fin-section">
    <h2>By Ticket Category</h2>
    <table class="fin-table">
        <thead>
            <tr>
                <th>Category</th>
                <th>Price (UGX)</th>
                <th>Tickets Sold</th>
                <th>Amount Collected (UGX)</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($byCategory as $row)
                <tr>
                    <td>{{ $row->ticketCategory?->name ?? '—' }}</td>
                    <td>{{ number_format((float) ($row->ticketCategory?->price ?? 0), 0) }}</td>
                    <td>{{ (int) $row->tickets }}</td>
                    <td class="amount">{{ number_format((float) $row->total, 0) }}</td>
                </tr>
            @empty
                <tr><td colspan="4" style="color:#6b7280;text-align:center;padding:1.5rem;">No confirmed payments yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
