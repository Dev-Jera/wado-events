<x-filament-widgets::widget>
    <div class="wado-payments-ops">
        <section class="wado-payments-hero">
            <div>
                <h2>Payments</h2>
                <p>Monitor payment status, failed transactions and ticket issuance across all events.</p>
            </div>

            <div class="wado-payments-hero__meta">
                <button type="button" wire:click="$refresh" class="wado-sync-btn">Sync now</button>
                <article>
                    <span>LAST SYNC</span>
                    <strong>{{ $lastSync }}</strong>
                </article>
                <article>
                    <span>TOTAL COLLECTED</span>
                    <strong>UGX {{ number_format($totalCollected / 1000000, 2) }}M</strong>
                </article>
            </div>
        </section>

        <div class="wado-payments-ops__stats">
            <article class="wado-payments-card wado-payments-card--primary">
                <h3>Pending payments</h3>
                <p class="wado-payments-card__value">{{ number_format($pendingPayments) }}</p>
                <p class="wado-payments-card__hint">Awaiting provider confirmation callbacks.</p>
            </article>

            <article class="wado-payments-card">
                <h3>Failed payments</h3>
                <p class="wado-payments-card__value">{{ number_format($failedPayments) }}</p>
                <p class="wado-payments-card__hint">Needs customer retry or support follow-up.</p>
            </article>

            <article class="wado-payments-card">
                <h3>Confirmed, no ticket</h3>
                <p class="wado-payments-card__value">{{ number_format($confirmedNoTicket) }}</p>
                <p class="wado-payments-card__hint">{{ number_format($ticketsPendingIssue) }} tickets still pending issuance.</p>
            </article>

            <article class="wado-payments-card">
                <h3>Confirmed today</h3>
                <p class="wado-payments-card__value">{{ number_format($confirmedToday) }}</p>
                <p class="wado-payments-card__hint">Across {{ number_format($eventsWithOpenPayments) }} events with open payment issues.</p>
            </article>
        </div>

        <section class="wado-payments-ops__guide">
            <h3>What to do</h3>
            <div class="wado-payments-ops__guide-grid">
                <article>
                    <span>PENDING</span>
                    <h4>Long-stuck</h4>
                    <p>Verify provider reference and check webhook delivery logs first.</p>
                </article>
                <article>
                    <span>FAILED</span>
                    <h4>Action needed</h4>
                    <p>Contact the attendee and ask for a fresh payment attempt.</p>
                </article>
                <article>
                    <span>CONFIRMED, NO TICKET</span>
                    <h4>Use resend</h4>
                    <p>Use the <strong>Resend</strong> button on each row to retry ticket issuance.</p>
                </article>
            </div>
        </section>
    </div>

    <style>
        .wado-payments-ops {
            display: grid;
            gap: 0.9rem;
            --wado-navy-900: #0b1f4d;
            --wado-navy-800: #102a66;
            --wado-navy-700: #173980;
            --wado-black-900: #111827;
            --wado-black-700: #374151;
            --wado-white: #ffffff;
        }

        .wado-payments-hero {
            border-radius: 12px;
            border: 1px solid #dbe4f0;
            background: var(--wado-white);
            padding: 1rem 1.05rem;
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            align-items: stretch;
        }

        .wado-payments-hero h2 {
            margin: 0;
            font-size: 2rem;
            line-height: 1;
            color: var(--wado-black-900);
            font-weight: 800;
        }

        .wado-payments-hero p {
            margin: 0.45rem 0 0;
            max-width: 480px;
            color: var(--wado-black-700);
            font-size: 0.92rem;
            line-height: 1.35;
        }

        .wado-payments-hero__meta {
            display: grid;
            grid-template-columns: auto repeat(2, minmax(120px, 1fr));
            gap: 0.6rem;
            align-items: stretch;
        }

        .wado-sync-btn {
            border: 1px solid #0b1f4d;
            border-radius: 10px;
            background: #ffffff;
            color: #111827;
            font-size: 0.86rem;
            font-weight: 700;
            padding: 0 0.95rem;
            cursor: pointer;
            min-height: 100%;
        }

        .wado-sync-btn:hover {
            background: #f8fbff;
        }

        .wado-payments-hero__meta article {
            border: 1px solid #dbe4f0;
            border-radius: 10px;
            background: var(--wado-white);
            padding: 0.7rem 0.8rem;
            display: grid;
            gap: 0.35rem;
        }

        .wado-payments-hero__meta span {
            color: var(--wado-black-700);
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.04em;
        }

        .wado-payments-hero__meta strong {
            color: var(--wado-black-900);
            font-size: 1.3rem;
            line-height: 1.1;
            font-weight: 800;
        }

        .wado-payments-ops__stats {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 0.75rem;
        }

        .wado-payments-card {
            border: 1px solid #dbe4f0;
            border-radius: 12px;
            background: var(--wado-white);
            padding: 0.95rem 1rem;
        }

        .wado-payments-card--primary {
            background: var(--wado-navy-900);
            color: var(--wado-white);
            border-color: var(--wado-navy-900);
        }

        .wado-payments-card h3 {
            margin: 0;
            font-size: 0.86rem;
            color: var(--wado-black-700);
            font-weight: 700;
        }

        .wado-payments-card--primary h3,
        .wado-payments-card--primary .wado-payments-card__value,
        .wado-payments-card--primary .wado-payments-card__hint {
            color: var(--wado-white);
        }

        .wado-payments-card__value {
            margin: 0.35rem 0 0.2rem;
            font-size: 1.65rem;
            font-weight: 800;
            line-height: 1;
            color: var(--wado-black-900);
        }

        .wado-payments-card__hint {
            margin: 0;
            font-size: 0.79rem;
            color: var(--wado-black-700);
            line-height: 1.35;
        }

        .wado-payments-ops__guide {
            border: 1px solid #dbe4f0;
            border-radius: 12px;
            background: var(--wado-white);
            padding: 0.95rem 1rem;
        }

        .wado-payments-ops__guide h3 {
            margin: 0 0 0.45rem;
            font-size: 0.9rem;
            font-weight: 800;
            color: var(--wado-black-900);
        }

        .wado-payments-ops__guide-grid {
            display: grid;
            gap: 0.75rem;
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .wado-payments-ops__guide-grid article {
            border: 1px solid #dbe4f0;
            border-radius: 10px;
            background: #ffffff;
            padding: 0.75rem 0.8rem;
            color: var(--wado-black-900);
        }

        .wado-payments-ops__guide-grid span {
            display: inline-block;
            border: 1px solid #dbe4f0;
            border-radius: 999px;
            padding: 0.12rem 0.5rem;
            font-size: 0.67rem;
            font-weight: 800;
            letter-spacing: 0.03em;
            margin-bottom: 0.45rem;
            color: var(--wado-black-700);
        }

        .wado-payments-ops__guide-grid h4 {
            margin: 0 0 0.35rem;
            color: var(--wado-black-900);
            font-size: 0.9rem;
            font-weight: 800;
        }

        .wado-payments-ops__guide-grid p {
            margin: 0;
            color: var(--wado-black-700);
            font-size: 0.86rem;
            line-height: 1.4;
        }

        .wado-payments-ops__guide-grid strong {
            color: var(--wado-black-900);
        }

        @media (max-width: 1200px) {
            .wado-payments-hero {
                flex-direction: column;
            }

            .wado-payments-hero__meta {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .wado-sync-btn {
                grid-column: 1 / -1;
                min-height: 42px;
            }

            .wado-payments-ops__stats {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .wado-payments-ops__guide-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 700px) {
            .wado-payments-ops__stats {
                grid-template-columns: 1fr;
            }
        }
    </style>
</x-filament-widgets::widget>
