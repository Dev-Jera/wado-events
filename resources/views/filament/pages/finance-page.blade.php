<x-filament-panels::page>
@php
    $events       = $this->getEvents();
    $selectedEvent = $this->getSelectedEvent();
    $summary      = $this->getSummary();
    $byChannel    = $this->getByChannel();
    $byCategory   = $this->getByCategory();
@endphp

<style>
    .fin-cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
    .fin-card { background: #fff; border-radius: 12px; padding: 1.1rem 1.4rem; box-shadow: 0 1px 4px rgba(0,0,0,.08); border: 1px solid #f0f4fb; }
    .fin-card-label { font-size: .72rem; text-transform: uppercase; letter-spacing: .08em; color: #6b7280; font-weight: 700; }
    .fin-card-value { font-size: 1.6rem; font-weight: 800; margin-top: .25rem; font-variant-numeric: tabular-nums; }
    .fin-card-sub { font-size: .72rem; color: #9ca3af; margin-top: .1rem; }
    .c-green { color: #16a34a; } .c-red { color: #dc2626; } .c-amber { color: #d97706; } .c-blue { color: #1d4ed8; }
    .fin-table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,.08); border: 1px solid #f0f4fb; margin-bottom: 2rem; }
    .fin-table th { background: #1a3a8f; color: #e2e8f0; padding: .65rem 1rem; text-align: left; font-size: .75rem; text-transform: uppercase; letter-spacing: .06em; }
    .fin-table td { padding: .7rem 1rem; border-bottom: 1px solid #f1f5f9; font-size: .88rem; }
    .fin-table tr:last-child td { border-bottom: none; }
    .fin-table tr:hover td { background: #f8fafc; }
    .fin-table tfoot td { background: #f8fafc; font-weight: 700; border-top: 2px solid #e2e8f0; }
    .amount { font-weight: 700; font-variant-numeric: tabular-nums; }
    .ev-link { font-weight: 700; color: #1e293b; cursor: pointer; text-decoration: none; }
    .ev-link:hover { color: #f59e0b; }
    .back-btn { display: inline-flex; align-items: center; gap: .4rem; color: #f59e0b; font-weight: 700; cursor: pointer; font-size: .9rem; margin-bottom: 1.2rem; background: none; border: none; padding: 0; }
    .export-btn { float: right; background: #1e293b; color: #fff; padding: .45rem 1rem; border-radius: 8px; text-decoration: none; font-size: .82rem; font-weight: 700; }
    .export-btn:hover { background: #0f172a; }
    .badge { display: inline-block; padding: .18rem .55rem; border-radius: 999px; font-size: .72rem; font-weight: 800; }
    .badge-ok { background: #dcfce7; color: #16a34a; }
    .pbadge { display: inline-block; padding: .2rem .6rem; border-radius: 999px; font-size: .73rem; font-weight: 800; text-transform: uppercase; }
    .p-mtn { background: #fef9c3; color: #854d0e; } .p-airtel { background: #fee2e2; color: #991b1b; }
    .p-cash { background: #dcfce7; color: #166534; } .p-pos { background: #dbeafe; color: #1e40af; }
    .p-free, .p-default { background: #f3f4f6; color: #374151; }
    .fin-section-title { font-size: .78rem; text-transform: uppercase; letter-spacing: .08em; color: #6b7280; font-weight: 700; margin: 0 0 .75rem; }
    .sub { color: #6b7280; font-size: .78rem; }
</style>

@if ($selectedEvent)
    {{-- ── Event detail view ── --}}
    <div style="overflow:hidden;">
        <button class="back-btn" onclick="window.location='{{ request()->url() }}'">← All Events</button>
        <a href="{{ route('admin.finance.show', ['event' => $selectedEvent, 'export' => 1]) }}" class="export-btn">Export CSV</a>
        <div style="clear:both;margin-bottom:1.2rem;">
            <span class="sub">{{ $selectedEvent->starts_at?->format('d M Y, H:i') }} · {{ $selectedEvent->venue }}, {{ $selectedEvent->city }}</span>
        </div>
    </div>

    <div class="fin-cards">
        <div class="fin-card"><div class="fin-card-label">Collected</div><div class="fin-card-value c-green">{{ number_format((float)($summary->confirmed ?? 0), 0) }}</div><div class="fin-card-sub">UGX</div></div>
        <div class="fin-card"><div class="fin-card-label">Refunded</div><div class="fin-card-value c-red">{{ number_format((float)($summary->refunded ?? 0), 0) }}</div><div class="fin-card-sub">UGX</div></div>
        <div class="fin-card"><div class="fin-card-label">Net Revenue</div><div class="fin-card-value c-blue">{{ number_format(((float)($summary->confirmed ?? 0)) - ((float)($summary->refunded ?? 0)), 0) }}</div><div class="fin-card-sub">UGX</div></div>
        <div class="fin-card"><div class="fin-card-label">Pending</div><div class="fin-card-value c-amber">{{ number_format((float)($summary->pending ?? 0), 0) }}</div><div class="fin-card-sub">UGX</div></div>
        <div class="fin-card"><div class="fin-card-label">Tickets Sold</div><div class="fin-card-value">{{ (int)($summary->tickets_sold ?? 0) }}</div></div>
        <div class="fin-card"><div class="fin-card-label">Refunded Tickets</div><div class="fin-card-value c-red">{{ (int)($summary->tickets_refunded ?? 0) }}</div></div>
    </div>

    <p class="fin-section-title">By Payment Channel</p>
    <table class="fin-table">
        <thead><tr><th>Channel</th><th>Tickets</th><th>Collected (UGX)</th></tr></thead>
        <tbody>
            @forelse ($byChannel as $row)
                @php $p = strtolower($row->payment_provider); $cls = match($p) { 'mtn'=>'p-mtn','airtel'=>'p-airtel','cash'=>'p-cash','pos'=>'p-pos','free'=>'p-free',default=>'p-default' }; @endphp
                <tr><td><span class="pbadge {{ $cls }}">{{ strtoupper($p) }}</span></td><td>{{ (int)$row->tickets }}</td><td class="amount">{{ number_format((float)$row->total, 0) }}</td></tr>
            @empty
                <tr><td colspan="3" style="color:#9ca3af;text-align:center;padding:1.5rem;">No confirmed payments yet.</td></tr>
            @endforelse
        </tbody>
    </table>

    <p class="fin-section-title">By Ticket Category</p>
    <table class="fin-table">
        <thead><tr><th>Category</th><th>Price (UGX)</th><th>Tickets Sold</th><th>Collected (UGX)</th></tr></thead>
        <tbody>
            @forelse ($byCategory as $row)
                <tr><td>{{ $row->ticketCategory?->name ?? '—' }}</td><td>{{ number_format((float)($row->ticketCategory?->price ?? 0), 0) }}</td><td>{{ (int)$row->tickets }}</td><td class="amount">{{ number_format((float)$row->total, 0) }}</td></tr>
            @empty
                <tr><td colspan="4" style="color:#9ca3af;text-align:center;padding:1.5rem;">No confirmed payments yet.</td></tr>
            @endforelse
        </tbody>
    </table>

@else
    {{-- ── All events list ── --}}
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
                    $confirmed = (float)($event->revenue_confirmed ?? 0);
                    $refunded  = (float)($event->revenue_refunded ?? 0);
                    $pending   = (float)($event->revenue_pending ?? 0);
                    $net       = $confirmed - $refunded;
                @endphp
                <tr>
                    <td>
                        <a href="{{ request()->url() }}?event_id={{ $event->id }}" class="ev-link">{{ $event->title }}</a>
                        <div class="sub">{{ $event->starts_at?->format('d M Y') }} · {{ $event->venue }}, {{ $event->city }}</div>
                    </td>
                    <td><span class="badge badge-ok">{{ (int)($event->tickets_sold ?? 0) }}</span></td>
                    <td class="amount c-green">{{ $confirmed > 0 ? number_format($confirmed, 0) : '—' }}</td>
                    <td class="amount c-red">{{ $refunded > 0 ? number_format($refunded, 0) : '—' }}</td>
                    <td class="amount c-amber">{{ $pending > 0 ? number_format($pending, 0) : '—' }}</td>
                    <td class="amount">{{ number_format($net, 0) }}</td>
                    <td><a href="{{ request()->url() }}?event_id={{ $event->id }}" style="color:#f59e0b;font-weight:700;text-decoration:none;font-size:.85rem;">Details →</a></td>
                </tr>
            @empty
                <tr><td colspan="7" style="color:#9ca3af;text-align:center;padding:2rem;">No events found.</td></tr>
            @endforelse
        </tbody>
        @if ($events->isNotEmpty())
        <tfoot>
            <tr>
                <td>All Events Total</td>
                <td>{{ $events->sum('tickets_sold') }}</td>
                <td class="amount c-green">{{ number_format($events->sum('revenue_confirmed'), 0) }}</td>
                <td class="amount c-red">{{ ($r = $events->sum('revenue_refunded')) > 0 ? number_format($r, 0) : '—' }}</td>
                <td>—</td>
                <td class="amount">{{ number_format($events->sum('revenue_confirmed') - $events->sum('revenue_refunded'), 0) }}</td>
                <td></td>
            </tr>
        </tfoot>
        @endif
    </table>
@endif
</x-filament-panels::page>
