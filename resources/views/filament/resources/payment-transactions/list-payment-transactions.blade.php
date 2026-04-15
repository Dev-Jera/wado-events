<x-filament-panels::page>
    <div class="wado-payments-list-shell">
        {{ $this->table }}
    </div>

    <style>
        .wado-payments-list-shell {
            border-radius: 14px;
            overflow: hidden;
            border: 1px solid #dbe4f0;
            background: #ffffff;
        }

        .wado-payments-list-shell .fi-ta-header-toolbar {
            background: #ffffff;
            border-bottom: 1px solid #dbe4f0;
            padding: 0.8rem 1rem;
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
            border-radius: 10px;
            color: #111827;
            padding: 0.5rem 0.75rem;
        }

        .wado-payments-list-shell .fi-tabs-item .fi-tabs-item-label,
        .wado-payments-list-shell .fi-tabs-item > .fi-icon {
            color: #111827;
        }

        .wado-payments-list-shell .fi-tabs-item.fi-active {
            background: #ffffff;
            border-color: #dbe4f0;
        }

        .wado-payments-list-shell .fi-tabs-item.fi-active .fi-tabs-item-label,
        .wado-payments-list-shell .fi-tabs-item.fi-active > .fi-icon {
            color: #111827;
            font-weight: 700;
        }

        .fi-sidebar-item.fi-active .fi-sidebar-item-label,
        .fi-sidebar-item.fi-active .fi-sidebar-item-icon {
            color: #111827 !important;
        }

        .wado-payments-list-shell .fi-ta-table thead th {
            background: #f8fbff;
            color: #111827;
            border-bottom-color: #dbe4f0;
            font-weight: 700;
        }

        .wado-payments-list-shell .fi-badge {
            border-radius: 999px;
        }
    </style>
</x-filament-panels::page>
