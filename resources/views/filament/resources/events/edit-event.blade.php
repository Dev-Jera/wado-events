@php
    $stats  = $this->getEventStats();
    $record = $this->getRecord();
    $cover  = $record->image_url
        ? (str_starts_with($record->image_url, '/') ? $record->image_url : '/storage/' . $record->image_url)
        : null;
    $minPrice = $record->ticketCategories->min('price');
    $maxPrice = $record->ticketCategories->max('price');
    $isFree   = $record->ticketCategories->isNotEmpty() && $record->ticketCategories->every(fn($t) => (float)$t->price <= 0);
    $priceLabel = $isFree ? 'Free' : ('UGX ' . number_format($minPrice) . ($maxPrice > $minPrice ? ' – ' . number_format($maxPrice) : ''));

    $statusColor = match($record->status) {
        'published' => ['bg' => '#dcfce7', 'text' => '#16a34a'],
        'cancelled' => ['bg' => '#fee2e2', 'text' => '#dc2626'],
        default     => ['bg' => '#fef9c3', 'text' => '#ca8a04'],
    };
@endphp

<x-filament-panels::page>

<div class="wado-edit-layout">

    {{-- LEFT — Stats + Form --}}
    <div class="wado-edit-left">

        {{-- Stats strip --}}
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

        {{-- Form Area --}}
        <div class="wado-form-wrap">
            {{ $this->content }}
        </div>

    </div>

    {{-- RIGHT — Live Preview --}}
    <div class="wado-edit-right">
        <div class="wado-preview-card">

            <div class="wado-preview-label">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" width="13" height="13"><path d="M10 12.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z"/><path fill-rule="evenodd" d="M.664 10.59a1.651 1.651 0 0 1 0-1.186A10.004 10.004 0 0 1 10 3c4.257 0 7.893 2.66 9.336 6.41.147.381.146.804 0 1.186A10.004 10.004 0 0 1 10 17c-4.257 0-7.893-2.66-9.336-6.41ZM14 10a4 4 0 1 1-8 0 4 4 0 0 1 8 0Z" clip-rule="evenodd"/></svg>
                Public preview
            </div>

            <div class="wado-preview-cover">
                @if($cover)
                    <img src="{{ $cover }}" alt="{{ $record->title }}" />
                @else
                    <div class="wado-preview-cover-placeholder">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" width="40" height="40"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" /></svg>
                        <span>No cover image</span>
                    </div>
                @endif>
                <span class="wado-preview-status" style="background:{{ $statusColor['bg'] }}; color:{{ $statusColor['text'] }}">
                    {{ ucfirst($record->status) }}
                </span>
            </div>

            <div class="wado-preview-body">
                @if($record->category)
                    <span class="wado-preview-category">{{ strtoupper($record->category->name) }}</span>
                @endif
                <h2 class="wado-preview-title">{{ $record->title ?: 'Untitled event' }}</h2>
                
                @if($record->starts_at)
                    <div class="wado-preview-meta">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" width="14" height="14"><path fill-rule="evenodd" d="M5.75 2a.75.75 0 0 1 .75.75V4h7V2.75a.75.75 0 0 1 1.5 0V4h.25A2.75 2.75 0 0 1 18 6.75v8.5A2.75 2.75 0 0 1 15.25 18H4.75A2.75 2.75 0 0 1 2 15.25v-8.5A2.75 2.75 0 0 1 4.75 4H5V2.75A.75.75 0 0 1 5.75 2Zm-1 5.5c-.69 0-1.25.56-1.25 1.25v6.5c0 .69.56 1.25 1.25 1.25h10.5c.69 0 1.25-.56 1.25-1.25v-6.5c0-.69-.56-1.25-1.25-1.25H4.75Z" clip-rule="evenodd"/></svg>
                        {{ $record->starts_at->format('D, M j, Y · g:i A') }}
                    </div>
                @endif

                @if($record->venue)
                    <div class="wado-preview-meta">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" width="14" height="14"><path fill-rule="evenodd" d="m9.69 18.933.003.001C9.89 19.02 10 19 10 19s.11.02.308-.066l.002-.001.006-.003.018-.008a5.741 5.741 0 0 0 .281-.14c.186-.096.446-.24.757-.433.62-.384 1.445-.966 2.274-1.765C15.302 14.988 17 12.493 17 9A7 7 0 1 0 3 9c0 3.492 1.698 5.988 3.355 7.584a13.731 13.731 0 0 0 2.273 1.765 11.842 11.842 0 0 0 .976.544l.062.029.018.008.006.003ZM10 11.25a2.25 2.25 0 1 0 0-4.5 2.25 2.25 0 0 0 0 4.5Z" clip-rule="evenodd"/></svg>
                        {{ $record->venue }}{{ $record->city ? ', ' . $record->city : '' }}
                    </div>
                @endif

                @if($record->ticketCategories->isNotEmpty())
                    <div class="wado-preview-tickets">
                        <div class="wado-preview-tickets-heading">Tickets</div>
                        @foreach($record->ticketCategories->take(4) as $tc)
                            <div class="wado-preview-ticket-row">
                                <span class="wado-preview-ticket-name">{{ $tc->name }}</span>
                                <span class="wado-preview-ticket-price">{{ (float)$tc->price <= 0 ? 'Free' : 'UGX ' . number_format($tc->price) }}</span>
                            </div>
                        @endforeach
                        @if($record->ticketCategories->count() > 4)
                            <div class="wado-preview-ticket-more">+{{ $record->ticketCategories->count() - 4 }} more</div>
                        @endif
                    </div>
                @endif

                <div class="wado-preview-cta">
                    <div class="wado-preview-price-tag">{{ $priceLabel }}</div>
                    <button class="wado-preview-btn" type="button" disabled>Get Tickets</button>
                </div>
            </div>
        </div>

        <div class="wado-preview-links">
            <a href="{{ route('events.show', $record) }}" target="_blank" class="wado-preview-link">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" width="13" height="13"><path d="M12.232 4.232a2.5 2.5 0 0 1 3.536 3.536l-1.225 1.224a.75.75 0 0 0 1.061 1.06l1.224-1.224a4 4 0 0 0-5.656-5.656l-3 3a4 4 0 0 0 .225 5.865.75.75 0 0 0 .977-1.138 2.5 2.5 0 0 1-.142-3.667l3-3Z"/><path d="M11.603 7.963a.75.75 0 0 0-.977 1.138 2.5 2.5 0 0 1 .142 3.667l-3 3a2.5 2.5 0 0 1-3.536-3.536l1.225-1.224a.75.75 0 0 0-1.061-1.06l-1.224 1.224a4 4 0 1 0 5.656 5.656l3-3a4 4 0 0 0-.225-5.865Z"/></svg>
                View public page
            </a>
            <a href="{{ route('tickets.verify.index') }}" target="_blank" class="wado-preview-link">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" width="13" height="13"><path fill-rule="evenodd" d="M3.5 2A1.5 1.5 0 0 0 2 3.5V5c0 1.149.15 2.263.43 3.326a13.022 13.022 0 0 0 9.244 9.244c1.063.28 2.177.43 3.326.43h1.5a1.5 1.5 0 0 0 1.5-1.5v-1.148a1.5 1.5 0 0 0-1.175-1.465l-3.223-.716a1.5 1.5 0 0 0-1.767 1.052l-.267.933c-.117.41-.555.643-.95.48a11.542 11.542 0 0 1-6.254-6.254c-.163-.395.07-.833.48-.95l.933-.267a1.5 1.5 0 0 0 1.052-1.767l-.716-3.223A1.5 1.5 0 0 0 4.648 2H3.5Z" clip-rule="evenodd"/></svg>
                Gate scanner
            </a>
        </div>
    </div>

</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const markAsStyled = (el) => {
        if (el instanceof HTMLElement) {
            el.dataset.wadoStyled = '1';
        }
    };

    const isStyled = (el) => el instanceof HTMLElement && el.dataset.wadoStyled === '1';

    function fixFilamentWidths() {
        const formWrap = document.querySelector('.wado-form-wrap');
        if (!formWrap) return;

        const grids = formWrap.querySelectorAll('.fi-grid, .fi-sc, .fi-sc-tabs, .fi-section, .fi-section-content');
        grids.forEach(el => {
            if (isStyled(el)) {
                return;
            }

            el.style.setProperty('display', 'block', 'important');
            el.style.setProperty('width', '100%', 'important');
            el.style.setProperty('max-width', '100%', 'important');
            markAsStyled(el);
        });

        const columns = formWrap.querySelectorAll('.fi-grid-col');
        columns.forEach(col => {
            if (isStyled(col)) {
                return;
            }

            col.style.setProperty('display', 'block', 'important');
            col.style.setProperty('width', '100%', 'important');
            col.style.setProperty('max-width', '100%', 'important');
            col.style.setProperty('flex', 'none', 'important');
            markAsStyled(col);
        });

        const form = formWrap.querySelector('form');
        if (form && !isStyled(form)) {
            form.style.setProperty('width', '100%', 'important');
            form.style.setProperty('display', 'block', 'important');
            markAsStyled(form);
        }
    }

    fixFilamentWidths();
    setTimeout(fixFilamentWidths, 150);

    const formWrap = document.querySelector('.wado-form-wrap');
    if (!formWrap) {
        return;
    }

    const observer = new MutationObserver(fixFilamentWidths);
    observer.observe(formWrap, {
        childList: true, 
        subtree: true,
    });
});
</script>

</x-filament-panels::page>

<style>
/* ====================== GLOBAL RESET ====================== */
.fi-page-content,
.fi-main,
.fi-width-7xl {
    max-width: 100% !important;
    width: 100% !important;
    padding: 0 !important;
}

/* ====================== LAYOUT ====================== */
.wado-edit-layout {
    display: flex;
    gap: 2rem;
    align-items: flex-start;
    width: 100%;
}

.wado-edit-left {
    flex: 1;
    min-width: 0;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 1.75rem;
    box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.07);
}

.wado-edit-right {
    width: 320px;
    flex-shrink: 0;
    position: sticky;
    top: 1.75rem;
}

/* ====================== STATS STRIP ====================== */
.wado-edit-stats {
    display: flex;
    gap: 1px;
    background: #e2e8f0;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    overflow: hidden;
    margin-bottom: 1.5rem;
}

.wado-edit-stat {
    flex: 1;
    padding: 1rem 1.25rem;
    background: #fff;
}

.wado-edit-stat__label {
    font-size: 0.65rem;
    font-weight: 700;
    letter-spacing: 0.08em;
    color: #94a3b8;
    text-transform: uppercase;
    display: block;
    margin-bottom: 0.25rem;
}

.wado-edit-stat__value {
    font-size: 1.55rem;
    font-weight: 800;
    color: #0f172a;
    line-height: 1;
}

.wado-edit-stat--revenue .wado-edit-stat__value {
    color: #c8102e;
}

/* ====================== FORM STYLING ====================== */
.wado-form-wrap {
    width: 100%;
}

.wado-form-wrap .fi-grid,
.wado-form-wrap .fi-sc,
.wado-form-wrap .fi-section,
.wado-form-wrap form {
    width: 100% !important;
    max-width: 100% !important;
}

.wado-form-wrap .fi-grid-col {
    width: 100% !important;
    max-width: 100% !important;
    flex: none !important;
}

.wado-form-wrap .fi-section {
    margin-bottom: 1.75rem;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    background: #fff;
    overflow: hidden;
}

.wado-form-wrap .fi-section-header {
    background: #f8fafc;
    padding: 1.1rem 1.4rem;
    border-bottom: 1px solid #e2e8f0;
}

.wado-form-wrap .fi-section-header h3 {
    font-size: 1.1rem;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
}

.wado-form-wrap .fi-input,
.wado-form-wrap .fi-select,
.wado-form-wrap .fi-textarea,
.wado-form-wrap .fi-rich-editor {
    border-radius: 10px;
    border: 1.5px solid #cbd5e1;
    transition: all 0.2s ease;
}

.wado-form-wrap .fi-input:focus,
.wado-form-wrap .fi-select:focus,
.wado-form-wrap .fi-textarea:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.12);
}

.wado-form-wrap .fi-label {
    font-weight: 600;
    color: #334155;
    margin-bottom: 0.4rem;
    font-size: 0.9rem;
}

.wado-form-wrap .fi-field-wrp {
    margin-bottom: 1.4rem;
}

.wado-form-wrap .fi-btn {
    border-radius: 10px;
    padding: 0.7rem 1.4rem;
    font-weight: 600;
}

/* ====================== PREVIEW CARD ====================== */
.wado-preview-card {
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    background: #fff;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(13, 27, 62, 0.06);
}

.wado-preview-label {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    font-size: 0.68rem;
    font-weight: 700;
    text-transform: uppercase;
    color: #94a3b8;
    padding: 0.75rem 1.25rem 0.6rem;
    border-bottom: 1px solid #f1f5f9;
    background: #fafbfc;
}

.wado-preview-cover {
    position: relative;
    width: 100%;
    height: 160px;
    background: #f1f5f9;
    overflow: hidden;
}

.wado-preview-cover img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.wado-preview-cover-placeholder {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100%;
    gap: 0.5rem;
    color: #cbd5e1;
    font-size: 0.75rem;
}

.wado-preview-status {
    position: absolute;
    top: 12px;
    right: 12px;
    font-size: 0.65rem;
    font-weight: 700;
    text-transform: uppercase;
    padding: 0.25rem 0.65rem;
    border-radius: 9999px;
}

.wado-preview-body {
    padding: 1.1rem 1.25rem 1.25rem;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.wado-preview-category {
    display: inline-block;
    font-size: 0.62rem;
    font-weight: 800;
    letter-spacing: 0.08em;
    color: #f59e0b;
    background: rgba(245, 158, 11, 0.12);
    border: 1px solid rgba(245, 158, 11, 0.3);
    border-radius: 6px;
    padding: 0.2rem 0.55rem;
}

.wado-preview-title {
    font-size: 1.15rem;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.3;
    margin: 0;
}

.wado-preview-meta {
    display: flex;
    align-items: flex-start;
    gap: 0.45rem;
    font-size: 0.8rem;
    color: #64748b;
    line-height: 1.4;
}

.wado-preview-tickets {
    border: 1px solid #f1f5f9;
    border-radius: 10px;
    overflow: hidden;
    margin-top: 0.25rem;
}

.wado-preview-tickets-heading {
    font-size: 0.65rem;
    font-weight: 700;
    text-transform: uppercase;
    color: #94a3b8;
    padding: 0.55rem 0.9rem;
    background: #f8fafc;
    border-bottom: 1px solid #f1f5f9;
}

.wado-preview-ticket-row {
    display: flex;
    justify-content: space-between;
    padding: 0.45rem 0.9rem;
    font-size: 0.8rem;
    border-bottom: 1px solid #f1f5f9;
}

.wado-preview-ticket-row:last-child {
    border-bottom: none;
}

.wado-preview-ticket-name { color: #374151; }
.wado-preview-ticket-price { color: #0f172a; font-weight: 700; }

.wado-preview-cta {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
    margin-top: 0.5rem;
    padding-top: 0.75rem;
    border-top: 1px solid #f1f5f9;
}

.wado-preview-price-tag {
    font-size: 1.05rem;
    font-weight: 800;
    color: #0f172a;
}

.wado-preview-btn {
    background: #c8102e;
    color: #fff;
    border: none;
    border-radius: 10px;
    font-size: 0.82rem;
    font-weight: 700;
    padding: 0.55rem 1.25rem;
    cursor: not-allowed;
    opacity: 0.9;
}

/* ====================== LINKS ====================== */
.wado-preview-links {
    display: flex;
    flex-direction: column;
    gap: 0.6rem;
    margin-top: 1rem;
}

.wado-preview-link {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.8rem;
    font-weight: 600;
    color: #64748b;
    text-decoration: none;
    padding: 0.7rem 1rem;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    transition: all 0.2s;
}

.wado-preview-link:hover {
    color: #0f172a;
    border-color: #334155;
}

/* ====================== RESPONSIVE ====================== */
@media (max-width: 1150px) {
    .wado-edit-layout { 
        flex-direction: column; 
        gap: 1.5rem;
    }
    .wado-edit-right { 
        width: 100%; 
        position: static; 
    }
    .wado-edit-left { 
        width: 100%; 
    }
}
</style>