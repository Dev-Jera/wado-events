<x-filament-panels::page>

    <div class="ev-layout">

        {{-- ══ LEFT: events table ══════════════════════════════════════ --}}
        <div class="ev-table-col">
            {{ $this->table }}
        </div>

        {{-- ══ RIGHT: persistent detail panel ════════════════════════ --}}
        <div class="ev-detail-col">
            @php $stats = $this->getSelectedEventStats(); @endphp

            @if (empty($stats))
                <div class="ev-empty">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="1.5"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    <p>Click on an event to view its details</p>
                </div>
            @else
                @include('filament.resources.events.event-detail-panel', $stats)
            @endif
        </div>

    </div>

    <style>
    @import url('https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&display=swap');

    /* Page-only brand tone for /dashboard/events */
    .fi-page {
        --ev-brand-blue: #0a4fbe;
        --ev-brand-blue-dark: #083f98;
        --ev-brand-blue-soft: #e8f0ff;

        /* Override Filament primary color scale on this page only */
        --primary-50: oklch(0.97 0.02 255);
        --primary-100: oklch(0.94 0.04 255);
        --primary-200: oklch(0.89 0.07 255);
        --primary-300: oklch(0.82 0.11 255);
        --primary-400: oklch(0.74 0.15 255);
        --primary-500: oklch(0.62 0.20 259);
        --primary-600: oklch(0.56 0.22 261);
        --primary-700: oklch(0.50 0.20 262);
        --primary-800: oklch(0.42 0.16 263);
        --primary-900: oklch(0.36 0.12 264);
        --primary-950: oklch(0.27 0.09 266);
    }

    /* Pull events page content upward */
    .fi-page .fi-page-header-main-ctn {
        padding-block: .25rem !important;
    }

    .fi-page .fi-header {
        margin-top: -.35rem !important;
        margin-bottom: .35rem !important;
    }

    /* ── Two-column split layout ──────────────────────────────── */
    .ev-layout {
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: 1rem;
        align-items: start;
        font-family: 'Quicksand', 'Nunito', 'Plus Jakarta Sans', sans-serif;
    }
    @media (max-width: 1100px) {
        .ev-layout { grid-template-columns: 1fr; }
        .ev-detail-col { border-top: 2px solid #e2e8f0; padding-top: 1rem; }
    }

    /* ── Table column ─────────────────────────────────────────── */
    .ev-table-col { min-width: 0; }

    /* ── Detail column ────────────────────────────────────────── */
    .ev-detail-col {
        position: sticky;
        top: 1.5rem;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        background: #fff;
        overflow: hidden;
        max-height: calc(100vh - 120px);
        overflow-y: auto;
    }

    /* ── Empty state ──────────────────────────────────────────── */
    .ev-empty {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: .75rem;
        padding: 3rem 2rem;
        color: #94a3b8;
        font-size: .8rem;
        font-family: 'Quicksand', 'Nunito', 'Plus Jakarta Sans', sans-serif;
    }

    /* Highlight the selected row ─────────────────────────────── */
    .ev-table-col .fi-ta-row.ev-selected-row > td {
        background: var(--ev-brand-blue-soft) !important;
    }

    .fi-page .fi-btn-color-primary,
    .fi-page .fi-btn-primary {
        background-color: var(--ev-brand-blue) !important;
        border-color: var(--ev-brand-blue) !important;
        color: #ffffff !important;
    }

    .fi-page .fi-btn-color-primary:hover,
    .fi-page .fi-btn-primary:hover {
        background-color: var(--ev-brand-blue-dark) !important;
        border-color: var(--ev-brand-blue-dark) !important;
    }

    /* Ensure header create action matches the same blue tone */
    .fi-page .fi-header .fi-btn,
    .fi-page .fi-header .fi-btn-color-primary,
    .fi-page .fi-header .fi-ac-btn-action {
        background-color: var(--ev-brand-blue) !important;
        border-color: var(--ev-brand-blue) !important;
        color: #ffffff !important;
    }

    .fi-page .fi-header .fi-btn:hover,
    .fi-page .fi-header .fi-btn-color-primary:hover,
    .fi-page .fi-header .fi-ac-btn-action:hover {
        background-color: var(--ev-brand-blue-dark) !important;
        border-color: var(--ev-brand-blue-dark) !important;
    }
    </style>

</x-filament-panels::page>
