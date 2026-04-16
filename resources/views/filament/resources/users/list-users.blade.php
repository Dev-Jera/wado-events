<x-filament-panels::page>
    <div class="wado-users-list-shell">
        {{ $this->table }}
    </div>

    <style>
        .fi-page {
            --users-blue: #0a4fbe;
            --users-blue-dark: #083f98;
            --users-blue-soft: #eef4ff;
            --users-border: #dbe4f0;
        }

        /* Push this page upward */
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

        .wado-users-list-shell,
        .wado-users-list-shell *:not(svg):not(path):not(circle):not(rect):not(line):not(polyline):not(polygon) {
            font-family: var(--wado-admin-font, 'Quicksand', 'Nunito', sans-serif);
        }

        .wado-users-list-shell {
            border-radius: 14px;
            overflow: hidden;
            border: 1px solid var(--users-border);
            background: #ffffff;
            box-shadow: 0 10px 24px rgba(10, 79, 190, 0.05);
        }

        .wado-users-list-shell .fi-ta-header-toolbar {
            background: #ffffff;
            border-bottom: 1px solid var(--users-border);
            padding: 0.8rem 1rem;
        }

        .wado-users-list-shell .fi-tabs {
            background: #f8fbff;
            border-bottom: 1px solid var(--users-border);
        }

        /* Make the brown Create button blue on this page */
        .fi-page .fi-header .fi-btn,
        .fi-page .fi-header .fi-btn-color-primary,
        .fi-page .fi-header .fi-btn-primary,
        .fi-page .fi-header .fi-ac-btn-action,
        .wado-users-list-shell .fi-btn-color-primary,
        .wado-users-list-shell .fi-btn-primary {
            background: var(--users-blue) !important;
            border-color: var(--users-blue) !important;
            color: #ffffff !important;
        }

        .fi-page .fi-header .fi-btn:hover,
        .fi-page .fi-header .fi-btn-color-primary:hover,
        .fi-page .fi-header .fi-btn-primary:hover,
        .fi-page .fi-header .fi-ac-btn-action:hover,
        .wado-users-list-shell .fi-btn-color-primary:hover,
        .wado-users-list-shell .fi-btn-primary:hover {
            background: var(--users-blue-dark) !important;
            border-color: var(--users-blue-dark) !important;
        }

        .wado-users-list-shell .fi-dropdown-list-item:hover,
        .wado-users-list-shell .fi-ta-record:hover td {
            background: var(--users-blue-soft) !important;
        }
    </style>
</x-filament-panels::page>
