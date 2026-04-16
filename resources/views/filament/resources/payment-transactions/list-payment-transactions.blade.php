<x-filament-panels::page>
    <div class="wado-payments-list-shell">
        {{ $this->table }}
    </div>

    <style>
        .fi-page {
            --pay-blue: #0a4fbe;
            --pay-blue-dark: #083f98;
            --pay-blue-soft: #eef4ff;
            --pay-blue-border: #c9d9f8;
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

        /* Pull this page content upward */
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

        .wado-payments-list-shell,
        .wado-payments-list-shell *:not(svg):not(path):not(circle):not(rect):not(line):not(polyline):not(polygon) {
            font-family: var(--wado-admin-font, 'Quicksand', 'Nunito', sans-serif);
        }

        .wado-payments-list-shell {
            border-radius: 14px;
            overflow: hidden;
            border: 1px solid #dbe4f0;
            background: #ffffff;
            box-shadow: 0 10px 24px rgba(10, 79, 190, 0.05);
        }

        .wado-payments-list-shell .fi-ta-header-toolbar {
            background: #ffffff;
            border-bottom: 1px solid #dbe4f0;
            padding: 0.8rem 1rem;
        }

        .wado-payments-list-shell .fi-input-wrp,
        .wado-payments-list-shell .fi-select-input,
        .wado-payments-list-shell .fi-input {
            border-radius: 12px !important;
        }

        .wado-payments-list-shell .fi-ta-header-toolbar .fi-input,
        .wado-payments-list-shell .fi-ta-header-toolbar .fi-select-input,
        .wado-payments-list-shell .fi-ta-header-toolbar .fi-input-wrp {
            background: #ffffff;
        }

        .wado-payments-list-shell .fi-tabs {
            background: #f8fbff;
            border-bottom: 1px solid #dbe4f0;
            padding: 0.6rem 0.75rem;
            gap: 0.35rem;
            overflow: auto;
        }

        .wado-payments-list-shell .fi-tabs-item {
            background: #ffffff;
            border: 1px solid #dbe4f0;
            border-radius: 999px;
            color: #4a5f80;
            padding: 0.4rem 0.68rem;
            font-size: 0.7rem;
        }

        .wado-payments-list-shell .fi-tabs-item .fi-tabs-item-label,
        .wado-payments-list-shell .fi-tabs-item > .fi-icon {
            color: #4a5f80;
            font-size: 0.7rem;
        }

        .wado-payments-list-shell .fi-tabs-item.fi-active {
            background: var(--pay-blue-soft);
            border-color: var(--pay-blue-border);
        }

        .wado-payments-list-shell .fi-tabs-item.fi-active .fi-tabs-item-label,
        .wado-payments-list-shell .fi-tabs-item.fi-active > .fi-icon {
            color: var(--pay-blue);
            font-weight: 600;
        }

        .fi-sidebar-item.fi-active .fi-sidebar-item-label,
        .fi-sidebar-item.fi-active .fi-sidebar-item-icon {
            color: #111827 !important;
        }

        .wado-payments-list-shell .fi-ta-table thead th {
            background: #f8fbff;
            color: #5a7397;
            border-bottom-color: #dbe4f0;
            font-weight: 600;
            font-size: .7rem;
            letter-spacing: .01em;
            text-transform: none;
        }

        .wado-payments-list-shell .fi-ta-table tbody td {
            font-size: .74rem;
            color: #1f3658;
        }

        .wado-payments-list-shell .fi-badge {
            border-radius: 999px;
            padding-inline: .55rem;
        }

        .wado-payments-list-shell .fi-ta-header-toolbar .fi-btn,
        .wado-payments-list-shell .fi-btn-color-primary,
        .wado-payments-list-shell .fi-btn-primary {
            background: var(--pay-blue) !important;
            border-color: var(--pay-blue) !important;
            color: #ffffff !important;
        }

        .wado-payments-list-shell .fi-ta-header-toolbar .fi-btn:hover,
        .wado-payments-list-shell .fi-btn-color-primary:hover,
        .wado-payments-list-shell .fi-btn-primary:hover {
            background: var(--pay-blue-dark) !important;
            border-color: var(--pay-blue-dark) !important;
        }

        .wado-payments-list-shell .fi-dropdown-list-item:hover,
        .wado-payments-list-shell .fi-ta-record:hover td {
            background: var(--pay-blue-soft) !important;
        }
    </style>
</x-filament-panels::page>
