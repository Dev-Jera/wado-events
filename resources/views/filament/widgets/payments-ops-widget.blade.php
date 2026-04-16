<x-filament-widgets::widget>
    <div class="pay-ops">
        <section class="pay-toolbar">
            <div class="pay-scope">
                <p class="pay-kicker">PAYMENTS OVERVIEW</p>
                <h3>{{ $selectedEvent?->title ?? 'All events' }}</h3>
                <p>{{ $selectedEvent ? 'All summary metrics and transaction rows below are filtered to this event.' : 'Choose an event to focus the page, or keep all events selected for a global operations view.' }}</p>
            </div>

            <form method="GET" action="{{ route('filament.admin.resources.payment-transactions.index') }}" class="pay-toolbar-actions">
                <label class="pay-field">
                    <span>Event</span>
                    <select name="event_id" onchange="this.form.submit()">
                        <option value="">All events</option>
                        @foreach ($events as $event)
                            <option value="{{ $event->id }}" @selected($selectedEventId === (int) $event->id)>{{ $event->title }}</option>
                        @endforeach
                    </select>
                </label>

                <article class="pay-meta-card">
                    <span>Last sync</span>
                    <strong>{{ $lastSync }}</strong>
                </article>

                <button type="button" wire:click="$refresh" class="pay-sync-btn">Sync now</button>
            </form>
        </section>

        <section class="pay-stats-row">
            <article class="pay-stat-card pay-stat-card-primary">
                <h4>Pending payments</h4>
                <strong>{{ number_format($pendingPayments) }}</strong>
                <p>Awaiting provider confirmation callbacks.</p>
            </article>

            <article class="pay-stat-card">
                <h4>Failed payments</h4>
                <strong>{{ number_format($failedPayments) }}</strong>
                <p>Needs customer retry or support follow-up.</p>
            </article>

            <article class="pay-stat-card">
                <h4>Confirmed, no ticket</h4>
                <strong>{{ number_format($confirmedNoTicket) }}</strong>
                <p>{{ number_format($ticketsPendingIssue) }} tickets still pending issuance.</p>
            </article>

            <article class="pay-stat-card">
                <h4>Confirmed today</h4>
                <strong>{{ number_format($confirmedToday) }}</strong>
                <p>{{ number_format($eventsWithOpenPayments) }} {{ \Illuminate\Support\Str::plural('event', $eventsWithOpenPayments) }} with open payment issues.</p>
            </article>
        </section>
    </div>

    <style>
        .pay-ops {
            --pay-blue: #0a4fbe;
            --pay-blue-dark: #083f98;
            --pay-blue-soft: #eef4ff;
            --pay-border: #dbe4f0;
            --pay-text: #132744;
            --pay-muted: #667a98;
            display: grid;
            gap: 0.85rem;
            font-family: var(--wado-admin-font, 'Quicksand', 'Nunito', sans-serif);
        }

        .pay-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: start;
            gap: 1rem;
            padding: 0.95rem 1rem;
            border: 1px solid var(--pay-border);
            border-radius: 14px;
            background: #ffffff;
        }

        .pay-scope {
            display: grid;
            gap: 0.18rem;
            max-width: 44rem;
        }

        .pay-kicker {
            margin: 0;
            font-size: 0.64rem;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: #7184a2;
            font-weight: 700;
        }

        .pay-scope h3 {
            margin: 0;
            font-size: 1.05rem;
            line-height: 1.1;
            font-weight: 800;
            color: var(--pay-text);
        }

        .pay-scope p {
            margin: 0;
            font-size: 0.76rem;
            color: var(--pay-muted);
            line-height: 1.4;
        }

        .pay-toolbar-actions {
            display: grid;
            grid-template-columns: minmax(220px, 1fr) minmax(140px, auto) auto;
            gap: 0.6rem;
            align-items: stretch;
            min-width: min(100%, 540px);
        }

        .pay-field {
            display: grid;
            gap: 0.26rem;
        }

        .pay-field span {
            font-size: 0.64rem;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: #6d7f9e;
        }

        .pay-field select {
            height: 44px;
            border: 1px solid var(--pay-border);
            border-radius: 12px;
            padding: 0 0.85rem;
            background: #ffffff;
            color: var(--pay-text);
            font-size: 0.76rem;
            font-weight: 600;
            font-family: inherit;
        }

        .pay-meta-card {
            border: 1px solid var(--pay-border);
            border-radius: 12px;
            background: #ffffff;
            padding: 0.62rem 0.8rem;
            display: grid;
            gap: 0.18rem;
            align-content: center;
        }

        .pay-meta-card span {
            font-size: 0.63rem;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: #6d7f9e;
        }

        .pay-meta-card strong {
            font-size: 0.92rem;
            line-height: 1.1;
            color: var(--pay-text);
            font-weight: 800;
        }

        .pay-sync-btn {
            border: 1px solid var(--pay-blue);
            border-radius: 12px;
            background: var(--pay-blue);
            color: #ffffff;
            font-size: 0.76rem;
            font-weight: 700;
            padding: 0 1rem;
            font-family: inherit;
            cursor: pointer;
            min-height: 44px;
        }

        .pay-sync-btn:hover {
            background: var(--pay-blue-dark);
            border-color: var(--pay-blue-dark);
        }

        .pay-stats-row {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 0.75rem;
        }

        .pay-stat-card {
            border: 1px solid var(--pay-border);
            border-radius: 14px;
            background: #ffffff;
            padding: 0.85rem 0.95rem;
            display: grid;
            gap: 0.2rem;
        }

        .pay-stat-card-primary {
            background: var(--pay-blue);
            border-color: var(--pay-blue);
        }

        .pay-stat-card h4 {
            margin: 0;
            font-size: 0.8rem;
            color: #223a5e;
            font-weight: 700;
        }

        .pay-stat-card strong {
            font-size: 1.3rem;
            line-height: 1;
            color: var(--pay-text);
            font-weight: 800;
        }

        .pay-stat-card p {
            margin: 0;
            font-size: 0.74rem;
            color: #5f7392;
            line-height: 1.35;
        }

        .pay-stat-card-primary h4,
        .pay-stat-card-primary strong,
        .pay-stat-card-primary p {
            color: #ffffff;
        }

        @media (max-width: 1180px) {
            .pay-toolbar {
                flex-direction: column;
            }

            .pay-toolbar-actions {
                width: 100%;
                grid-template-columns: 1fr 1fr auto;
                min-width: 0;
            }

            .pay-stats-row {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 760px) {
            .pay-toolbar-actions {
                grid-template-columns: 1fr;
            }

            .pay-stats-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</x-filament-widgets::widget>
