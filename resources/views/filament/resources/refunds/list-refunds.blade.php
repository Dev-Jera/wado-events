<x-filament-panels::page>
    <div class="wado-refunds-list-shell">
        {{ $this->table }}
    </div>

    <style>
        .fi-page {
            --refund-blue-soft: #eef4ff;
            --refund-border: #dbe4f0;
        }

        /* Pull this page upward */
        .fi-page .fi-page-header-main-ctn {
            padding-block: .2rem !important;
            row-gap: .45rem !important;
        }

        .fi-page .fi-header {
            margin-top: -.35rem !important;
            margin-bottom: .35rem !important;
        }

        .fi-page .fi-page-content {
            row-gap: .7rem !important;
        }

        .wado-refunds-list-shell,
        .wado-refunds-list-shell *:not(svg):not(path):not(circle):not(rect):not(line):not(polyline):not(polygon) {
            font-family: var(--wado-admin-font, 'Quicksand', 'Nunito', sans-serif);
        }

        .wado-refunds-list-shell {
            border-radius: 14px;
            overflow: hidden;
            border: 1px solid var(--refund-border);
            background: #ffffff;
            box-shadow: 0 10px 24px rgba(10, 79, 190, 0.05);
        }

        .wado-refunds-list-shell .fi-ta-header-toolbar {
            background: #ffffff;
            border-bottom: 1px solid var(--refund-border);
            padding: 0.8rem 1rem;
        }

        .wado-refunds-list-shell .fi-tabs {
            background: #f8fbff;
            border-bottom: 1px solid var(--refund-border);
            padding: 0.6rem 0.75rem;
            gap: 0.35rem;
            overflow: auto;
        }

        .wado-refunds-list-shell .fi-tabs-item.fi-active {
            background: var(--refund-blue-soft);
        }
    </style>
</x-filament-panels::page>
