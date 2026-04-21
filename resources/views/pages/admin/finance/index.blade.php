@extends('layouts.admin')

@section('title', 'Finance')
@section('heading', 'Finance')

@section('content')
<style>
    .fin-table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 14px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,.07); }
    .fin-table th { background: #08111f; color: #d7e1f2; padding: .75rem 1rem; text-align: left; font-size: .8rem; text-transform: uppercase; letter-spacing: .06em; }
    .fin-table td { padding: .75rem 1rem; border-bottom: 1px solid #f0f4fb; font-size: .9rem; }
    .fin-table tr:last-child td { border-bottom: none; }
    .fin-table tr:hover td { background: #f7f9fd; }
    .amount { font-weight: 700; font-variant-numeric: tabular-nums; }
    .confirmed { color: #16a34a; }
    .refunded { color: #dc2626; }
    .pending { color: #d97706; }
    .event-link { color: #0f1b31; font-weight: 700; text-decoration: none; }
    .event-link:hover { color: #b45309; }
    .badge { display: inline-block; padding: .2rem .6rem; border-radius: 999px; font-size: .75rem; font-weight: 700; }
    .badge-ok { background: #dcfce7; color: #16a34a; }
    .sub { color: #6b7280; font-size: .8rem; }
    .fin-total-row td { background: #f7f9fd; font-weight: 700; }
</style>

<table class="fin-table">
    <thead>
        <tr>
            <th>Event</th>
            <th>Tickets Sold</th>
            <th>Collected (UGX)</th>
            <th>Refunded (UGX)</th>
            <th>Pending (UGX)</th>
            <th>Net (UGX)</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @forelse ($events as $event)
            @php
                $confirmed = (float) ($event->revenue_confirmed ?? 0);
                $refunded  = (float) ($event->revenue_refunded ?? 0);
                $pending   = (float) ($event->revenue_pending ?? 0);
                $net       = $confirmed - $refunded;
            @endphp
            <tr>
                <td>
                    <a href="{{ route('admin.finance.show', $event) }}" class="event-link">{{ $event->title }}</a>
                    <div class="sub">{{ $event->starts_at?->format('d M Y') }} · {{ $event->venue }}, {{ $event->city }}</div>
                </td>
                <td><span class="badge badge-ok">{{ (int) ($event->tickets_sold ?? 0) }}</span></td>
                <td class="amount confirmed">{{ number_format($confirmed, 0) }}</td>
                <td class="amount refunded">{{ $refunded > 0 ? number_format($refunded, 0) : '—' }}</td>
                <td class="amount pending">{{ $pending > 0 ? number_format($pending, 0) : '—' }}</td>
                <td class="amount">{{ number_format($net, 0) }}</td>
                <td><a href="{{ route('admin.finance.show', $event) }}" style="color:#b45309;font-weight:700;text-decoration:none;">Details →</a></td>
            </tr>
        @empty
            <tr><td colspan="7" style="text-align:center;color:#6b7280;padding:2rem;">No events found.</td></tr>
        @endforelse
    </tbody>
    @if ($events->isNotEmpty())
        @php
            $totalConfirmed = $events->sum('revenue_confirmed');
            $totalRefunded  = $events->sum('revenue_refunded');
            $totalNet       = $totalConfirmed - $totalRefunded;
        @endphp
        <tfoot>
            <tr class="fin-total-row">
                <td>All Events Total</td>
                <td>{{ $events->sum('tickets_sold') }}</td>
                <td class="amount confirmed">{{ number_format($totalConfirmed, 0) }}</td>
                <td class="amount refunded">{{ $totalRefunded > 0 ? number_format($totalRefunded, 0) : '—' }}</td>
                <td>—</td>
                <td class="amount">{{ number_format($totalNet, 0) }}</td>
                <td></td>
            </tr>
        </tfoot>
    @endif
</table>
@endsection
