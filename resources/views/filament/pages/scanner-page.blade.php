<x-filament-panels::page>
    <div class="sp-wrap">
        <div class="sp-head">
            <h2>Scanner</h2>
            <a href="{{ route('tickets.verify.index', ['scanner_only' => 1, 'back' => url('/dashboard/scanner-page')]) }}" target="_blank" rel="noopener">Open full page</a>
        </div>

        <iframe
            class="sp-frame"
            src="{{ route('tickets.verify.index', ['embedded' => 1, 'back' => url('/dashboard/scanner-page')]) }}"
            title="Ticket Scanner"
            loading="lazy"
        ></iframe>
    </div>

    <style>

        .sp-wrap {
            display: grid;
            gap: .75rem;
            font-family: var(--wado-admin-font, 'Quicksand', 'Nunito', 'Plus Jakarta Sans', 'Segoe UI', sans-serif);
        }

        .sp-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
        }

        .sp-head h2 {
            margin: 0;
            color: #0f172a;
            font-size: 1rem;
            font-weight: 700;
        }

        .sp-head a {
            color: #0a4fbe;
            font-size: .78rem;
            font-weight: 600;
            text-decoration: none;
        }

        .sp-frame {
            width: 100%;
            min-height: 78vh;
            border: 1px solid #dbe5f2;
            border-radius: 12px;
            background: #fff;
        }

        @media (max-width: 640px) {
            .sp-head {
                align-items: flex-start;
                flex-direction: column;
            }

            .sp-head a {
                display: inline-flex;
                justify-content: center;
                width: 100%;
            }

            .sp-frame {
                min-height: 86vh;
            }
        }
    </style>
</x-filament-panels::page>
