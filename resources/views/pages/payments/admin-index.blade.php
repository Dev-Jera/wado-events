@extends('layouts.app')

@section('content')
    @php
        $statuses = ['INITIATED', 'PENDING', 'CONFIRMED', 'FAILED', 'REFUNDED'];
    @endphp

    <section class="pm-admin-page">
        <div class="pm-admin-shell">
            <div class="pm-head">
                <div>
                    <p>PAYMENT OPERATIONS</p>
                    <h1>Payment Monitor</h1>
                </div>
                <a href="{{ route('home') }}">Back home</a>
            </div>

            <form method="GET" class="pm-filters">
                <input type="text" name="q" value="{{ $searchTerm }}" placeholder="Search by ref, phone, user, event">
                <select name="status">
                    <option value="">All statuses</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status }}" @selected($statusFilter === $status)>{{ $status }}</option>
                    @endforeach
                </select>
                <button type="submit">Apply</button>
            </form>

            <div class="pm-stats">
                @foreach ($statuses as $status)
                    <div class="pm-stat {{ strtolower($status) }}">
                        <strong>{{ $status }}</strong>
                        <span>{{ (int) ($statusCounts[$status] ?? 0) }}</span>
                    </div>
                @endforeach
            </div>

            <div class="pm-table-wrap">
                <table class="pm-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Status</th>
                            <th>User</th>
                            <th>Event</th>
                            <th>Amount</th>
                            <th>Provider</th>
                            <th>Phone</th>
                            <th>Ref</th>
                            <th>Ticket</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($payments as $payment)
                            <tr>
                                <td>#{{ $payment->id }}</td>
                                <td><span class="pm-badge {{ strtolower($payment->status) }}">{{ $payment->status }}</span></td>
                                <td>{{ $payment->user?->name }}<br><small>{{ $payment->user?->email }}</small></td>
                                <td>{{ $payment->event?->title }}</td>
                                <td>UGX {{ number_format((float) $payment->total_amount, 0) }}</td>
                                <td>{{ strtoupper((string) $payment->payment_provider) }}</td>
                                <td>{{ $payment->phone_number ?: 'N/A' }}</td>
                                <td>
                                    <div>{{ $payment->idempotency_key }}</div>
                                    @if ($payment->provider_reference)
                                        <small>{{ $payment->provider_reference }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if ($payment->ticket_id)
                                        <a href="{{ route('tickets.show', $payment->ticket_id) }}">Ticket #{{ $payment->ticket_id }}</a>
                                    @else
                                        Pending issue
                                    @endif
                                </td>
                                <td>
                                    @if ($payment->status === 'CONFIRMED')
                                        <div style="display:grid;gap:6px;">
                                            <form method="POST" action="{{ route('payments.admin.resend', $payment) }}">
                                                @csrf
                                                <button type="submit">Resend</button>
                                            </form>
                                            <form method="POST" action="{{ route('payments.admin.refund', $payment) }}" onsubmit="return confirm('Refund payment #{{ $payment->id }}? This will cancel the ticket and update inventory where applicable.')">
                                                @csrf
                                                <input type="text" name="reason" required maxlength="500" placeholder="Refund reason" style="width:100%;margin-bottom:4px;border:1px solid #d1d5db;border-radius:6px;padding:4px 6px;font-size:12px;">
                                                <button type="submit" style="background:#b91c1c;color:#fff;border:none;padding:4px 10px;border-radius:6px;cursor:pointer;font-size:12px">
                                                    Refund
                                                </button>
                                            </form>
                                        </div>
                                    @elseif (in_array($payment->status, ['PENDING', 'INITIATED']))
                                        <div style="display:grid;gap:6px;">
                                            <form method="POST" action="{{ route('payments.admin.confirm', $payment) }}"
                                                  onsubmit="return confirm('Manually confirm payment #{{ $payment->id }} and issue ticket?')">
                                                @csrf
                                                <button type="submit" style="background:#15803d;color:#fff;border:none;padding:4px 10px;border-radius:6px;cursor:pointer;font-size:12px">
                                                    Force confirm
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('payments.admin.refund', $payment) }}" onsubmit="return confirm('Refund payment #{{ $payment->id }}? This will release reserved tickets.')">
                                                @csrf
                                                <input type="text" name="reason" required maxlength="500" placeholder="Refund reason" style="width:100%;margin-bottom:4px;border:1px solid #d1d5db;border-radius:6px;padding:4px 6px;font-size:12px;">
                                                <button type="submit" style="background:#b91c1c;color:#fff;border:none;padding:4px 10px;border-radius:6px;cursor:pointer;font-size:12px">
                                                    Refund
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="10">No payment transactions found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pm-pagination">
                {{ $payments->links() }}
            </div>
        </div>
    </section>

    <style>
        .pm-admin-page { min-height: 100vh; background: #f3f7fd; padding: 8rem 1rem 3rem; }
        .pm-admin-shell { width: min(1260px, calc(100% - 2rem)); margin: 0 auto; }
        .pm-head { display: flex; justify-content: space-between; align-items: end; gap: 1rem; margin-bottom: 1rem; }
        .pm-head p { margin: 0; color: #000000; font-size: .73rem; letter-spacing: .12em; font-weight: 700; }
        .pm-head h1 { margin: .3rem 0 0; color: #16335a; }
        .pm-head a { color: #1f64cd; font-weight: 700; text-decoration: none; }
        .pm-filters { display: grid; grid-template-columns: 1fr 180px auto; gap: .6rem; margin-bottom: .8rem; }
        .pm-filters input, .pm-filters select { height: 42px; border: 1px solid #cfdced; border-radius: 10px; padding: 0 .75rem; }
        .pm-filters button { height: 42px; border: 0; border-radius: 10px; background: #1c67d6; color: #fff; font-weight: 700; padding: 0 .9rem; }
        .pm-stats { display: grid; grid-template-columns: repeat(5, 1fr); gap: .55rem; margin-bottom: .75rem; }
        .pm-stat { background: #fff; border: 1px solid #d8e4f5; border-radius: 12px; padding: .65rem .7rem; display: grid; gap: .2rem; }
        .pm-stat strong { font-size: .7rem; color: #000000; }
        .pm-stat span { font-size: 1.2rem; font-weight: 800; color: #17375f; }
        .pm-table-wrap { overflow: auto; background: #fff; border: 1px solid #d8e4f5; border-radius: 14px; }
        .pm-table { width: 100%; border-collapse: collapse; min-width: 1120px; }
        .pm-table th, .pm-table td { border-bottom: 1px solid #e7eef8; padding: .6rem .65rem; text-align: left; font-size: .76rem; color: #214164; vertical-align: top; }
        .pm-table th { background: #f5f9ff; color: #5a7397; font-size: .66rem; letter-spacing: .09em; text-transform: uppercase; }
        .pm-badge { border-radius: 999px; padding: .2rem .48rem; font-size: .62rem; font-weight: 700; border: 1px solid; display: inline-flex; }
        .pm-badge.initiated, .pm-badge.pending { background: #eef4ff; border-color: #c7dafb; color: #2158a3; }
        .pm-badge.confirmed { background: #edf9f2; border-color: #bde8cb; color: #15784b; }
        .pm-badge.failed { background: #fff1f3; border-color: #f3c5cc; color: #9e2034; }
        .pm-badge.refunded { background: #fff6eb; border-color: #ffd7a8; color: #93540d; }
        .pm-table button { border: 0; border-radius: 8px; background: #1c67d6; color: #fff; font-size: .68rem; padding: .36rem .62rem; font-weight: 700; cursor: pointer; }
        .pm-pagination { margin-top: .85rem; }
        @media (max-width: 980px) {
            .pm-filters { grid-template-columns: 1fr; }
            .pm-stats { grid-template-columns: repeat(2, 1fr); }
        }
    </style>
@endsection
