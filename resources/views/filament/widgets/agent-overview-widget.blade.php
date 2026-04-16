<x-filament-widgets::widget>
    <div class="ag-shell">
        <section class="ag-hero">
            <div>
                <p class="ag-kicker">AGENT CONSOLE</p>
                <h3>Gate Operations</h3>
                <p>Track scan flow and walk-in payment pressure in real time.</p>
            </div>
            <a href="{{ route('tickets.verify.index') }}" target="_blank" rel="noopener" class="ag-open-link">Open Scanner View</a>
        </section>

        <section class="ag-grid">
            <article class="ag-card">
                <span>Scans Today</span>
                <strong>{{ number_format($scansToday) }}</strong>
                <small>Total attempts by your station</small>
            </article>
            <article class="ag-card">
                <span>Valid Scans</span>
                <strong>{{ number_format($validScansToday) }}</strong>
                <small>Accepted entries</small>
            </article>
            <article class="ag-card">
                <span>Rejected Scans</span>
                <strong>{{ number_format($failedScansToday) }}</strong>
                <small>Duplicates or invalid tickets</small>
            </article>
            <article class="ag-card">
                <span>Pending Walk-ins</span>
                <strong>{{ number_format($pendingWalkinPayments) }}</strong>
                <small>Payments waiting confirmation</small>
            </article>
        </section>
    </div>

    <style>
        .ag-shell {
            display: grid;
            gap: 0.75rem;
            font-family: var(--wado-admin-font, 'Quicksand', 'Nunito', sans-serif);
        }

        .ag-hero {
            border: 1px solid #dbe4f0;
            border-radius: 12px;
            background: linear-gradient(132deg, #0f2f83 0%, #1f58bd 100%);
            color: #fff;
            padding: 0.85rem 0.95rem;
            display: flex;
            align-items: start;
            justify-content: space-between;
            gap: 0.8rem;
            flex-wrap: wrap;
        }

        .ag-kicker {
            margin: 0;
            font-size: 0.62rem;
            letter-spacing: 0.11em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.75);
            font-weight: 700;
        }

        .ag-hero h3 {
            margin: 0.18rem 0 0;
            font-size: 1.02rem;
            line-height: 1.1;
            font-weight: 800;
        }

        .ag-hero p {
            margin: 0.28rem 0 0;
            font-size: 0.72rem;
            color: rgba(255, 255, 255, 0.85);
            max-width: 46ch;
        }

        .ag-open-link {
            text-decoration: none;
            border: 1px solid rgba(255, 255, 255, 0.3);
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            border-radius: 10px;
            padding: 0.46rem 0.64rem;
            font-size: 0.69rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .ag-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 0.68rem;
        }

        .ag-card {
            border: 1px solid #dbe4f0;
            border-radius: 12px;
            background: #fff;
            padding: 0.72rem 0.76rem;
            display: grid;
            gap: 0.2rem;
        }

        .ag-card span {
            color: #536684;
            font-size: 0.67rem;
            font-weight: 600;
        }

        .ag-card strong {
            color: #12253f;
            font-size: 1.25rem;
            font-weight: 800;
            line-height: 1;
        }

        .ag-card small {
            color: #7c8da7;
            font-size: 0.64rem;
            line-height: 1.35;
        }

        @media (max-width: 1100px) {
            .ag-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 560px) {
            .ag-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</x-filament-widgets::widget>
