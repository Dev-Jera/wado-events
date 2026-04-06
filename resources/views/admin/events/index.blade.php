@extends('layouts.admin')

@section('title', 'Events Dashboard')
@section('heading', 'Events Overview')

@section('content')
    @if (session('success'))
        <div class="flash-success">{{ session('success') }}</div>
    @endif

    <section class="stats-grid">
        <article class="stat-card"><span>Total events</span><strong>{{ $stats['total'] }}</strong></article>
        <article class="stat-card"><span>Published</span><strong>{{ $stats['published'] }}</strong></article>
        <article class="stat-card"><span>Drafts</span><strong>{{ $stats['drafts'] }}</strong></article>
        <article class="stat-card"><span>Total capacity</span><strong>{{ number_format($stats['capacity']) }}</strong></article>
    </section>

    <section class="panel">
        <div class="panel-head">
            <div>
                <h2>Manage events</h2>
                <p>Every event created from the admin form appears here and on the public events page.</p>
            </div>
            <a href="{{ route('admin.events.create') }}" class="primary-btn">Create event</a>
        </div>

        <div class="table-wrap">
            <table class="events-table">
                <thead>
                    <tr>
                        <th>Event</th><th>Status</th><th>Schedule</th><th>Venue</th><th>From</th><th>Tickets</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($events as $event)
                        <tr>
                            <td><strong>{{ $event->title }}</strong><div>{{ $event->category?->name ?? 'Uncategorized' }}</div></td>
                            <td><span class="status status-{{ $event->status }}">{{ ucfirst($event->status) }}</span></td>
                            <td>{{ $event->starts_at->format('d M Y, h:i A') }}</td>
                            <td>{{ $event->venue }}, {{ $event->city }}</td>
                            <td>UGX {{ number_format((float) $event->ticket_price, 2) }}</td>
                            <td>
                                {{ $event->tickets_available }} / {{ $event->capacity }}
                                @if ($event->ticketCategories->isNotEmpty())
                                    <div>{{ $event->ticketCategories->count() }} ticket groups</div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6">No events yet. Create your first one.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <style>
        .flash-success, .panel, .stat-card { background: #fff; border-radius: 24px; box-shadow: 0 18px 40px rgba(15, 23, 42, 0.07); }
        .flash-success { padding: 1rem 1.2rem; color: #166534; margin-bottom: 1rem; border: 1px solid #bbf7d0; background: #f0fdf4; }
        .stats-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 1rem; margin-bottom: 1rem; }
        .stat-card { padding: 1.4rem; }
        .stat-card span { display: block; color: #667085; margin-bottom: 0.6rem; }
        .stat-card strong { font-size: 2rem; }
        .panel { padding: 1.4rem; }
        .panel-head { display: flex; justify-content: space-between; align-items: end; gap: 1rem; margin-bottom: 1rem; }
        .panel-head h2 { margin: 0; }
        .panel-head p { margin: 0.35rem 0 0; color: #667085; }
        .primary-btn { display: inline-flex; align-items: center; justify-content: center; padding: 0.9rem 1.1rem; border-radius: 999px; background: linear-gradient(135deg, #f15a24, #dc2626); color: #fff; text-decoration: none; font-weight: 700; }
        .table-wrap { overflow-x: auto; }
        .events-table { width: 100%; border-collapse: collapse; }
        .events-table th, .events-table td { padding: 1rem 0.85rem; border-bottom: 1px solid #eaecf0; text-align: left; vertical-align: top; }
        .status { display: inline-flex; padding: 0.25rem 0.65rem; border-radius: 999px; font-size: 0.8rem; font-weight: 700; }
        .status-published { color: #166534; background: #dcfce7; }
        .status-draft { color: #92400e; background: #fef3c7; }
        .status-cancelled { color: #b91c1c; background: #fee2e2; }
        @media (max-width: 980px) { .stats-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } .panel-head { flex-direction: column; align-items: start; } }
        @media (max-width: 620px) { .stats-grid { grid-template-columns: 1fr; } }
    </style>
@endsection
