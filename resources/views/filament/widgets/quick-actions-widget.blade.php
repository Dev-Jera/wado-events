<x-filament-widgets::widget>
    <div class="qa-shell">
        <div class="qa-copy">
            <p class="qa-overline">WORKSPACE</p>
            <h3>{{ $consoleTitle }}</h3>
            <p>{{ $consoleHint }}</p>
        </div>

        <div class="qa-actions">
            @if($canCreateEvent)
                <a href="{{ $createEventUrl }}" class="qa-btn qa-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
                    Create Event
                </a>
            @endif
            @if($canUseGateTools)
                <a href="{{ $scannerUrl }}" class="qa-btn qa-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="6" height="6" rx="1"/><rect x="15" y="3" width="6" height="6" rx="1"/><rect x="3" y="15" width="6" height="6" rx="1"/><path d="M15 15h6v6"/></svg>
                    Scanner
                </a>
            @endif
            @if($canUseGateTools)
                <a href="{{ $gatePortalUrl }}" class="qa-btn qa-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3l8 4v5c0 5-3.5 8-8 9-4.5-1-8-4-8-9V7l8-4z"/></svg>
                    Gate Portal
                </a>
            @endif
            @if($canViewPayments)
                <a href="{{ $paymentsUrl }}" class="qa-btn qa-ghost">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>
                    Payments
                </a>
            @endif
        </div>
    </div>

    <style>
        .qa-shell {
            border: 1px solid #dbe4f0;
            border-radius: 12px;
            background: #ffffff;
            padding: 0.8rem 0.9rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.8rem;
            flex-wrap: wrap;
            font-family: var(--wado-admin-font, 'Quicksand', 'Nunito', sans-serif);
        }

        .qa-overline {
            margin: 0;
            font-size: 0.66rem;
            letter-spacing: 0.02em;
            text-transform: none;
            color: #607390;
            font-weight: 600;
        }

        .qa-copy h3 {
            margin: 0.16rem 0 0;
            color: #132a4c;
            font-size: 0.94rem;
            font-weight: 700;
            line-height: 1.1;
        }

        .qa-copy p {
            margin: 0.22rem 0 0;
            color: #627792;
            font-size: 0.72rem;
            line-height: 1.35;
            max-width: 58ch;
        }

        .qa-actions {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .qa-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.38rem;
            text-decoration: none;
            border-radius: 10px;
            border: 1px solid transparent;
            padding: 0.48rem 0.7rem;
            font-size: 0.72rem;
            font-weight: 600;
            transition: transform 0.12s ease, filter 0.12s ease;
            white-space: nowrap;
        }

        .qa-btn svg {
            width: 14px;
            height: 14px;
            flex-shrink: 0;
        }

        .qa-btn:hover {
            transform: translateY(-1px);
            filter: brightness(1.02);
        }

        .qa-primary {
            background: linear-gradient(135deg, #1e4ea9 0%, #326ed1 100%);
            color: #ffffff;
        }

        .qa-secondary {
            background: #edf3ff;
            border-color: #cfdcf8;
            color: #1b4fae;
        }

        .qa-ghost {
            background: #ffffff;
            border-color: #d4deec;
            color: #324c73;
        }

        @media (max-width: 640px) {
            .qa-shell {
                align-items: stretch;
                padding: 0.8rem;
            }

            .qa-actions {
                display: grid;
                grid-template-columns: 1fr;
                width: 100%;
            }

            .qa-btn {
                justify-content: center;
                width: 100%;
            }
        }
    </style>
</x-filament-widgets::widget>
