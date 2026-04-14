@php
    $stats = $this->getEventStats();
@endphp

<x-filament-panels::page>

    {{-- ── Stats strip ─────────────────────────────────────────────── --}}
    <div class="wado-edit-stats">
        <div class="wado-edit-stat">
            <span class="wado-edit-stat__label">CAPACITY</span>
            <span class="wado-edit-stat__value">{{ number_format($stats['capacity']) }}</span>
        </div>
        <div class="wado-edit-stat">
            <span class="wado-edit-stat__label">TICKETS SOLD</span>
            <span class="wado-edit-stat__value">{{ number_format($stats['ticketsSold']) }}</span>
        </div>
        <div class="wado-edit-stat wado-edit-stat--revenue">
            <span class="wado-edit-stat__label">REVENUE</span>
            <span class="wado-edit-stat__value">UGX {{ number_format($stats['revenue']) }}</span>
        </div>
    </div>

    {{-- ── Tabbed form ─────────────────────────────────────────────── --}}
    {{ $this->content }}

</x-filament-panels::page>

<style>
/* ── Stats strip ────────────────────────────────────────────── */
.wado-edit-stats {
    display: flex;
    gap: 1px;
    background: #e2e8f0;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    overflow: hidden;
    margin-bottom: .25rem;
}

.wado-edit-stat {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: .25rem;
    padding: .85rem 1.25rem;
    background: #fff;
}

.wado-edit-stat__label {
    font-size: .65rem;
    font-weight: 700;
    letter-spacing: .07em;
    color: #94a3b8;
    text-transform: uppercase;
}

.wado-edit-stat__value {
    font-size: 1.6rem;
    font-weight: 800;
    color: #0d1b3e;
    line-height: 1;
}

.wado-edit-stat--revenue .wado-edit-stat__value {
    color: #c8102e;
}

/* ── Tabs wrapper card ──────────────────────────────────────── */
/* The schema tabs container (.fi-sc-tabs) wraps nav + panels */
.fi-sc-tabs {
    background: #fff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 10px !important;
    overflow: hidden !important;
}

/* ── Tab nav bar (.fi-tabs is the <nav>) ────────────────────── */
.fi-sc-tabs > .fi-tabs {
    background: #f8fafc !important;
    border-radius: 0 !important;
    border-bottom: 1px solid #e2e8f0 !important;
    box-shadow: none !important;
    padding: 0 .75rem !important;
    gap: 0 !important;
    margin: 0 !important;
}

/* ── Individual tab buttons ─────────────────────────────────── */
.fi-sc-tabs .fi-tabs-item {
    border-bottom: 2px solid transparent !important;
    border-radius: 0 !important;
    padding-inline: .85rem !important;
    padding-block: .7rem !important;
    font-size: .82rem !important;
    font-weight: 600 !important;
    color: #64748b !important;
    background: transparent !important;
    margin-bottom: -1px !important;
}

.fi-sc-tabs .fi-tabs-item:hover {
    background: transparent !important;
    color: #0d1b3e !important;
}

.fi-sc-tabs .fi-tabs-item.fi-active {
    color: #0d1b3e !important;
    border-bottom-color: #c8102e !important;
    background: transparent !important;
}

.fi-sc-tabs .fi-tabs-item-label {
    color: inherit !important;
}

/* ── Tab content panels ─────────────────────────────────────── */
.fi-sc-tabs-tab {
    padding: 1.5rem !important;
}

/* ── Sections inside tabs — lighter header ──────────────────── */
.fi-sc-tabs-tab .fi-section-header {
    background: #f8fafc !important;
    border-bottom: 1px solid #e2e8f0 !important;
}

.fi-sc-tabs-tab .fi-section-header-heading {
    color: #0d1b3e !important;
    font-size: .75rem !important;
    text-transform: uppercase !important;
    letter-spacing: .06em !important;
}

.fi-sc-tabs-tab .fi-section-header svg {
    color: #64748b !important;
}

.fi-sc-tabs-tab .fi-section-header-description {
    color: #64748b !important;
    font-size: .72rem !important;
}
</style>
