@extends('layouts.app')

@section('content')
    <section class="gate-page">
        <div class="gate-shell">
            <div class="gate-head">
                <div>
                    <p>LIVE GATE METRICS</p>
                    <h1>Agent / Gate Portal</h1>
                </div>
                <div class="gate-head-actions">
                    <a href="{{ route('tickets.verify.index', $selectedEventId ? ['event_id' => $selectedEventId] : []) }}">Open scanner</a>
                    <button type="button" onclick="window.location.reload()">Refresh now</button>
                </div>
            </div>

            <div class="gate-note" role="status" aria-live="polite">
                Auto-refresh every 15 seconds. Metrics link event ticket allocation, QR-issued tickets, and scans.
            </div>

            <form method="GET" class="gate-filter">
                <label>
                    <span>View event category breakdown</span>
                    <select name="event_id" onchange="this.form.submit()">
                        <option value="0">Select event...</option>
                        @foreach ($events as $event)
                            <option value="{{ $event->id }}" @selected($selectedEventId === $event->id)>
                                {{ $event->title }} ({{ $event->starts_at?->format('d M, H:i') }})
                            </option>
                        @endforeach
                    </select>
                </label>
            </form>

            <div class="gate-table-wrap">
                <table class="gate-table">
                    <thead>
                        <tr>
                            <th>Event</th>
                            <th>Allocated</th>
                            <th>Inventory Remaining</th>
                            <th>QR Issued</th>
                            <th>Scanned</th>
                            <th>At Gate Remaining</th>
                            <th>Confirmed</th>
                            <th>Cancelled</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rows as $row)
                            <tr>
                                <td>
                                    <strong>{{ $row['event']->title }}</strong>
                                    <small>{{ $row['event']->venue }}, {{ $row['event']->city }} · {{ $row['event']->starts_at?->format('d M Y, H:i') }}</small>
                                </td>
                                <td>{{ $row['allocated'] }}</td>
                                <td>{{ $row['inventory_remaining'] }}</td>
                                <td>{{ $row['issued_with_qr'] }}</td>
                                <td>{{ $row['scanned'] }}</td>
                                <td><span class="badge warn">{{ $row['at_gate_remaining'] }}</span></td>
                                <td><span class="badge ok">{{ $row['confirmed'] }}</span></td>
                                <td><span class="badge bad">{{ $row['cancelled'] }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">No events found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($selected)
                <h2 class="gate-sub">Category Breakdown: {{ $selected['event']->title }}</h2>
                <div class="gate-table-wrap">
                    <table class="gate-table">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th>Allocated</th>
                                <th>Inventory Remaining</th>
                                <th>QR Issued</th>
                                <th>Scanned</th>
                                <th>At Gate Remaining</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($categoryRows as $row)
                                <tr>
                                    <td>{{ $row['category']->name }}</td>
                                    <td>{{ $row['allocated'] }}</td>
                                    <td>{{ $row['inventory_remaining'] }}</td>
                                    <td>{{ $row['issued_with_qr'] }}</td>
                                    <td>{{ $row['scanned'] }}</td>
                                    <td><span class="badge warn">{{ $row['at_gate_remaining'] }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </section>

    <style>
        .gate-page { min-height: 100vh; padding: 8rem 1rem 3rem; background: #f3f7fd; }
        .gate-shell { width: min(1280px, calc(100% - 2rem)); margin: 0 auto; }
        .gate-head { display: flex; justify-content: space-between; align-items: end; gap: 1rem; }
        .gate-head p { margin: 0; color: #000000; font-size: .73rem; letter-spacing: .12em; font-weight: 700; }
        .gate-head h1 { margin: .3rem 0 0; color: #15345d; }
        .gate-head-actions { display: flex; gap: .5rem; }
        .gate-head-actions a, .gate-head-actions button { border: 0; text-decoration: none; border-radius: 9px; padding: .58rem .82rem; font-weight: 700; background: #1b66d5; color: #fff; cursor: pointer; }
        .gate-head-actions a { background: #0d8b5a; }
        .gate-note { margin-top: .8rem; background: #eef5ff; border: 1px solid #cadcf7; border-radius: 12px; padding: .7rem .8rem; color: #2c4e78; font-size: .84rem; }
        .gate-filter { margin-top: .7rem; }
        .gate-filter label { display: grid; gap: .3rem; color: #294a73; font-weight: 700; }
        .gate-filter select { height: 42px; border: 1px solid #cfdced; border-radius: 10px; padding: 0 .72rem; }
        .gate-table-wrap { margin-top: .8rem; overflow: auto; border: 1px solid #d7e4f6; border-radius: 14px; background: #fff; }
        .gate-table { width: 100%; border-collapse: collapse; min-width: 980px; }
        .gate-table th, .gate-table td { border-bottom: 1px solid #e7eef8; padding: .62rem .66rem; text-align: left; color: #214064; font-size: .76rem; }
        .gate-table th { background: #f5f9ff; color: #000000; font-size: .64rem; letter-spacing: .09em; text-transform: uppercase; }
        .gate-table td strong { display: block; }
        .gate-table td small { color: #000000; }
        .badge { border-radius: 999px; padding: .18rem .46rem; border: 1px solid; font-weight: 700; font-size: .64rem; }
        .badge.ok { background: #edf9f2; color: #136f45; border-color: #bde8cb; }
        .badge.bad { background: #fff1f3; color: #9e2034; border-color: #f3c5cc; }
        .badge.warn { background: #fff6eb; color: #93540d; border-color: #ffd7a8; }
        .gate-sub { margin: 1rem 0 .4rem; color: #173b66; }
    </style>

    <script>
        setInterval(function () {
            window.location.reload();
        }, 15000);
    </script>
@endsection
