<x-filament-widgets::widget>
<div class="qa">
    <div class="qa-left">
        <div class="qa-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
        </div>
        <div>
            <span class="qa-kicker">WORKSPACE</span>
            <h3>{{ $consoleTitle }}</h3>
            <p>{{ $consoleHint }}</p>
        </div>
    </div>
    <div class="qa-actions">
        @if($canCreateEvent)
            <a href="{{ $createEventUrl }}" class="qa-btn qa-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><line x1="12" y1="14" x2="12" y2="18"/><line x1="10" y1="16" x2="14" y2="16"/></svg>
                Create Event
            </a>
        @endif
        @if($canUseGateTools)
            <a href="{{ $scannerUrl }}" class="qa-btn qa-blue">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="6" height="6" rx="1"/><rect x="15" y="3" width="6" height="6" rx="1"/><rect x="3" y="15" width="6" height="6" rx="1"/><path d="M15 15h.01M15 18h3M18 15v3M21 15h.01M21 18h.01"/></svg>
                Scanner
            </a>
        @endif
        @if($canUseGateTools)
            <a href="{{ $gatePortalUrl }}" class="qa-btn qa-blue">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l8 4v5c0 5-3.5 8-8 9-4.5-1-8-4-8-9V7l8-4z"/></svg>
                Gate Portal
            </a>
        @endif
        @if($canViewPayments)
            <a href="{{ $paymentsUrl }}" class="qa-btn qa-ghost">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                Payments
            </a>
        @endif
    </div>
</div>

<style>
.qa {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    padding: 1rem 1.1rem;
    background: #fff;
    border: 1.5px solid #e2e8f0;
    border-radius: 16px;
    box-shadow: 0 1px 4px rgba(15,23,42,.04);
    font-family: var(--wado-admin-font, 'Quicksand', sans-serif);
    flex-wrap: wrap;
}
.qa-left {
    display: flex;
    align-items: center;
    gap: 0.85rem;
}
.qa-icon {
    width: 42px; height: 42px;
    border-radius: 12px;
    background: #eff6ff;
    border: 1.5px solid #bfdbfe;
    color: #2563eb;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.qa-icon svg { width: 20px; height: 20px; }
.qa-kicker {
    display: block;
    font-size: 0.6rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    color: #94a3b8;
    margin-bottom: 0.15rem;
}
.qa-left h3 {
    margin: 0 0 0.18rem;
    font-size: 0.95rem;
    font-weight: 700;
    color: #0f172a;
    line-height: 1.2;
}
.qa-left p {
    margin: 0;
    font-size: 0.74rem;
    color: #64748b;
    line-height: 1.4;
    max-width: 52ch;
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
    gap: 0.4rem;
    text-decoration: none;
    border-radius: 10px;
    border: 1.5px solid transparent;
    padding: 0.55rem 0.9rem;
    font-size: 0.78rem;
    font-weight: 700;
    font-family: inherit;
    transition: transform .12s, box-shadow .12s, filter .12s;
    white-space: nowrap;
    cursor: pointer;
}
.qa-btn svg { width: 15px; height: 15px; flex-shrink: 0; }
.qa-btn:hover { transform: translateY(-1px); filter: brightness(1.04); box-shadow: 0 4px 12px rgba(0,0,0,.1); }

.qa-primary {
    background: linear-gradient(135deg, #1d4ed8, #2563eb);
    color: #fff;
    box-shadow: 0 2px 8px rgba(37,99,235,.3);
}
.qa-blue {
    background: #eff6ff;
    border-color: #bfdbfe;
    color: #1d4ed8;
}
.qa-ghost {
    background: #f8fafc;
    border-color: #e2e8f0;
    color: #475569;
}

@media (max-width: 860px) {
    .qa { flex-direction: column; align-items: stretch; }
    .qa-actions { display: grid; grid-template-columns: repeat(2, 1fr); }
    .qa-btn { justify-content: center; }
}
@media (max-width: 500px) {
    .qa-actions { grid-template-columns: 1fr; }
}
</style>
</x-filament-widgets::widget>
