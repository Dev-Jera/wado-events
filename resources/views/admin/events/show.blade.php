@extends('layouts.admin')

@section('title', 'Event Details')
@section('heading', 'Event Details')

@section('content')
    <section class="stats-grid">
        <article class="stat-card">
            <span>Tickets sold</span>
            <strong>{{ number_format($summary['tickets_sold']) }}</strong>
        </article>
        <article class="stat-card">
            <span>Unique buyers</span>
            <strong>{{ number_format($summary['buyers_count']) }}</strong>
        </article>
        <article class="stat-card">
            <span>Revenue</span>
            <strong>UGX {{ number_format($summary['revenue']) }}</strong>
        </article>
        <article class="stat-card">
            <span>Available</span>
            <strong>{{ number_format($summary['available']) }} / {{ number_format($summary['capacity']) }}</strong>
        </article>
    </section>

    <section class="content-grid">
        <div class="main-pane">
            <article class="panel event-hero">
                <p class="eyebrow">{{ $event->category?->name ?? 'Uncategorized' }}</p>
                <div class="hero-row">
                    <h2>{{ $event->title }}</h2>
                    <span class="status status-{{ $event->status }}">{{ ucfirst($event->status) }}</span>
                </div>
                <p class="hero-desc">{{ $event->description }}</p>
            </article>

            <article class="panel">
                <div class="panel-head">
                    <div>
                        <h3>Ticket buyers</h3>
                        <p>People who successfully bought tickets for this event.</p>
                    </div>
                </div>

                <div class="table-wrap">
                    <table class="buyers-table">
                        <thead>
                            <tr>
                                <th>Buyer</th>
                                <th>Category</th>
                                <th>Qty</th>
                                <th>Amount</th>
                                <th>Provider</th>
                                <th>Purchased</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($tickets as $ticket)
                                <tr>
                                    <td>
                                        <strong>{{ $ticket->user?->name ?? 'Unknown user' }}</strong>
                                        <div>{{ $ticket->user?->email ?? 'No email' }}</div>
                                    </td>
                                    <td>{{ $ticket->ticketCategory?->name ?? 'N/A' }}</td>
                                    <td>{{ number_format($ticket->quantity) }}</td>
                                    <td>UGX {{ number_format((float) $ticket->total_amount) }}</td>
                                    <td>{{ strtoupper((string) $ticket->payment_provider) }}</td>
                                    <td>{{ optional($ticket->purchased_at)->format('d M Y, h:i A') }}</td>
                                    <td><span class="status status-{{ $ticket->status }}">{{ ucfirst($ticket->status) }}</span></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7">No tickets purchased yet for this event.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($tickets->hasPages())
                    <div class="pagination-wrap">
                        {{ $tickets->links() }}
                    </div>
                @endif
            </article>
        </div>

        <aside class="side-pane">
            <article class="panel info-card">
                <h3>Event info</h3>
                <dl class="info-list">
                    <div><dt>Starts</dt><dd>{{ $event->starts_at?->format('d M Y, h:i A') }}</dd></div>
                    <div><dt>Ends</dt><dd>{{ $event->ends_at?->format('d M Y, h:i A') ?? 'N/A' }}</dd></div>
                    <div><dt>Venue</dt><dd>{{ $event->venue }}, {{ $event->city }}, {{ $event->country }}</dd></div>
                    <div><dt>Organizer</dt><dd>{{ $event->organizer?->name ?? 'N/A' }}</dd></div>
                    <div><dt>From price</dt><dd>{{ (float) $event->ticket_price <= 0 ? 'Free' : 'UGX ' . number_format((float) $event->ticket_price) }}</dd></div>
                </dl>
            </article>

            <article class="panel info-card">
                <h3>Ticket categories</h3>
                <ul class="category-list">
                    @forelse ($event->ticketCategories as $category)
                        <li>
                            <strong>{{ $category->name }}</strong>
                            <span>{{ (float) $category->price <= 0 ? 'Free' : 'UGX ' . number_format((float) $category->price) }}</span>
                            <small>{{ number_format($category->tickets_remaining) }} / {{ number_format($category->ticket_count) }} left</small>
                        </li>
                    @empty
                        <li><small>No ticket categories configured.</small></li>
                    @endforelse
                </ul>
            </article>
        </aside>
    </section>

    <style>
        .panel, .stat-card {
            background: #fff;
            border-radius: 24px;
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.07);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .stat-card {
            padding: 1.25rem;
        }

        .stat-card span {
            color: #64748b;
            display: block;
            margin-bottom: 0.5rem;
        }

        .stat-card strong {
            font-size: clamp(1.3rem, 2.2vw, 1.8rem);
        }

        .content-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 320px;
            gap: 1rem;
            align-items: start;
        }

        .main-pane,
        .side-pane {
            display: grid;
            gap: 1rem;
        }

        .event-hero {
            padding: 1.35rem;
        }

        .eyebrow {
            margin: 0;
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #b45309;
            font-weight: 700;
        }

        .hero-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            margin-top: 0.5rem;
        }

        .hero-row h2 {
            margin: 0;
            font-size: clamp(1.35rem, 2.6vw, 2rem);
        }

        .hero-desc {
            margin: 0.9rem 0 0;
            color: #475467;
            line-height: 1.55;
            white-space: pre-line;
        }

        .panel {
            padding: 1.25rem;
        }

        .panel-head h3 {
            margin: 0;
        }

        .panel-head p {
            margin: 0.35rem 0 0;
            color: #475467;
        }

        .table-wrap {
            overflow-x: auto;
            margin-top: 0.9rem;
        }

        .buyers-table {
            width: 100%;
            border-collapse: collapse;
        }

        .buyers-table th,
        .buyers-table td {
            border-bottom: 1px solid #eaecf0;
            padding: 0.8rem 0.65rem;
            text-align: left;
            vertical-align: top;
            white-space: nowrap;
        }

        .buyers-table td div {
            color: #64748b;
            font-size: 0.84rem;
            margin-top: 0.2rem;
        }

        .pagination-wrap {
            margin-top: 1rem;
        }

        .status {
            display: inline-flex;
            border-radius: 999px;
            padding: 0.24rem 0.65rem;
            font-size: 0.78rem;
            font-weight: 700;
        }

        .status-published,
        .status-confirmed {
            color: #166534;
            background: #dcfce7;
        }

        .status-draft {
            color: #92400e;
            background: #fef3c7;
        }

        .status-cancelled,
        .status-failed {
            color: #b91c1c;
            background: #fee2e2;
        }

        .info-card h3 {
            margin: 0 0 0.8rem;
        }

        .info-list {
            margin: 0;
            display: grid;
            gap: 0.6rem;
        }

        .info-list div {
            display: grid;
            gap: 0.2rem;
        }

        .info-list dt {
            color: #667085;
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            font-weight: 700;
        }

        .info-list dd {
            margin: 0;
            color: #101828;
            line-height: 1.4;
            font-weight: 600;
        }

        .category-list {
            list-style: none;
            margin: 0;
            padding: 0;
            display: grid;
            gap: 0.55rem;
        }

        .category-list li {
            border: 1px solid #eaecf0;
            border-radius: 14px;
            padding: 0.7rem 0.75rem;
            display: grid;
            gap: 0.2rem;
        }

        .category-list span {
            color: #1d4ed8;
            font-weight: 700;
            font-size: 0.9rem;
        }

        .category-list small {
            color: #64748b;
        }

        @media (max-width: 1100px) {
            .content-grid {
                grid-template-columns: 1fr;
            }

            .side-pane {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 860px) {
            .stats-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .side-pane {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 620px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }

            .hero-row {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
@endsection
