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
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

    /* ── Two-column split layout ──────────────────────────────── */
    .ev-layout {
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: 1rem;
        align-items: start;
        font-family: 'Plus Jakarta Sans', sans-serif;
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
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    /* Highlight the selected row ─────────────────────────────── */
    .ev-table-col .fi-ta-row.ev-selected-row > td {
        background: #eff6ff !important;
    }
    </style>

</x-filament-panels::page>
