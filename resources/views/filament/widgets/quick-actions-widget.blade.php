<x-filament-widgets::widget>
    <x-filament::section>
        <div class="qa-bar">
            <div class="qa-brand">
                <span class="qa-brand-label">Quick actions</span>
                <h3 class="qa-title">{{ $consoleTitle }}</h3>
                <p class="qa-subtitle">{{ $consoleHint }}</p>
            </div>
            <div class="qa-actions">
                @if($canCreateEvent)
                <a href="{{ $createEventUrl }}" class="qa-btn qa-btn--primary">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    + New event
                </a>
                @endif
                @if($canUseGateTools)
                <a href="{{ $scannerUrl }}" class="qa-btn qa-btn--secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="5" height="5" rx="1"/><rect x="16" y="3" width="5" height="5" rx="1"/><rect x="3" y="16" width="5" height="5" rx="1"/><path d="M21 16h-3a2 2 0 0 0-2 2v3"/><path d="M21 21v.01"/><path d="M12 7v3a2 2 0 0 1-2 2H7"/><path d="M3 12h.01"/><path d="M12 3h.01"/><path d="M12 16v.01"/><path d="M16 12h1"/><path d="M21 12v.01"/><path d="M12 21v-1"/></svg>
                    Gate Scanner
                </a>
                @endif
                @if($canViewPayments)
                <a href="{{ $paymentsUrl }}" class="qa-btn qa-btn--secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                    Payments
                </a>
                @endif
                @if($canUseGateTools)
                <a href="{{ $gatePortalUrl }}" class="qa-btn qa-btn--ghost">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    Gate Portal
                </a>
                @endif
            </div>
        </div>

        <style>
            .qa-bar {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 1rem;
                flex-wrap: wrap;
            }
            .qa-brand-label {
                font-size: .7rem;
                font-weight: 700;
                letter-spacing: .1em;
                text-transform: uppercase;
                color: var(--gray-400, #94a3b8);
            }
            .qa-title {
                margin: .18rem 0 0;
                font-size: .95rem;
                color: #e2e8f0;
                font-weight: 800;
            }
            .qa-subtitle {
                margin: .18rem 0 0;
                font-size: .72rem;
                color: #94a3b8;
            }
            .qa-actions {
                display: flex;
                gap: .5rem;
                flex-wrap: wrap;
            }
            .qa-btn {
                display: inline-flex;
                align-items: center;
                gap: .4rem;
                padding: .45rem .9rem;
                border-radius: 8px;
                font-size: .78rem;
                font-weight: 700;
                text-decoration: none;
                transition: opacity .15s, transform .1s;
                white-space: nowrap;
            }
            .qa-btn svg {
                width: 15px;
                height: 15px;
                flex-shrink: 0;
            }
            .qa-btn:hover { opacity: .85; transform: translateY(-1px); }
            .qa-btn--primary {
                background: #f8b26a;
                color: #1a0e00;
            }
            .qa-btn--secondary {
                background: color-mix(in srgb, #f8b26a 12%, transparent);
                color: #f8b26a;
                border: 1px solid color-mix(in srgb, #f8b26a 30%, transparent);
            }
            .qa-btn--ghost {
                background: transparent;
                color: var(--gray-400, #94a3b8);
                border: 1px solid var(--gray-700, #334155);
            }
        </style>
    </x-filament::section>
</x-filament-widgets::widget>
