<x-filament-panels::page>
    <div class="wado-create-grid">
        <main class="wado-create-main">
            <div class="wado-form-wrap">
                {{ $this->content }}
            </div>
        </main>

        <aside class="wado-create-aside">
            <section class="wado-side-card">
                <h3 class="wado-side-title">Cover image preview</h3>
                <div class="wado-cover-preview" id="wado-cover-preview">
                    <span>Click to upload a cover image</span>
                    <small>PNG, JPG, WEBP</small>
                </div>
            </section>

            <section class="wado-side-card">
                <h3 class="wado-side-title">Completion</h3>
                <div class="wado-progress-row">
                    <span id="wado-progress-label">Step 1 of 4</span>
                    <strong id="wado-progress-percent">25%</strong>
                </div>
                <div class="wado-progress-track">
                    <span id="wado-progress-bar"></span>
                </div>

                <ol class="wado-step-list" id="wado-step-list">
                    <li class="is-active">Event details</li>
                    <li>Cover image</li>
                    <li>Ticket categories</li>
                    <li>Settings</li>
                </ol>
            </section>

            <section class="wado-side-card">
                <h3 class="wado-side-title">Tips</h3>
                <div class="wado-tip-box">
                    <h4>Great event titles</h4>
                    <p>Keep it short, specific, and exciting. Include the artist name or theme if relevant.</p>
                </div>
                <div class="wado-tip-box">
                    <h4>Boost discoverability</h4>
                    <p>A detailed description with keywords helps attendees find your event faster.</p>
                </div>
            </section>
        </aside>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            function fixFilamentWidths() {
                const formWrap = document.querySelector('.wado-form-wrap');
                if (!formWrap) {
                    return;
                }

                const grids = formWrap.querySelectorAll('.fi-grid, .fi-sc, .fi-sc-tabs, .fi-section, .fi-section-content');
                grids.forEach((el) => {
                    el.style.setProperty('display', 'block', 'important');
                    el.style.setProperty('width', '100%', 'important');
                    el.style.setProperty('max-width', '100%', 'important');
                });

                const columns = formWrap.querySelectorAll('.fi-grid-col');
                columns.forEach((col) => {
                    col.style.setProperty('display', 'block', 'important');
                    col.style.setProperty('width', '100%', 'important');
                    col.style.setProperty('max-width', '100%', 'important');
                    col.style.setProperty('flex', 'none', 'important');
                });

                const form = formWrap.querySelector('form');
                if (form) {
                    form.style.setProperty('width', '100%', 'important');
                    form.style.setProperty('display', 'block', 'important');
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
                attributes: true,
            });
        });
    </script>

    <style>
        .fi-page-content,
        .fi-main,
        .fi-width-7xl {
            max-width: 100% !important;
            width: 100% !important;
        }

        .wado-create-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 330px;
            column-gap: 0;
            row-gap: 0;
            align-items: start;
        }

        .wado-create-main,
        .wado-create-main form,
        .wado-form-wrap,
        .wado-form-wrap form,
        .wado-create-main .fi-sc,
        .wado-create-main .fi-sc-tabs,
        .wado-create-main .fi-section {
            min-width: 0;
            width: 100% !important;
            max-width: 100% !important;
        }

        .wado-create-main {
            --container-3xs: 100%;
            --container-2xs: 100%;
            --container-xs: 100%;
            --container-sm: 100%;
            --container-md: 100%;
            --container-lg: 100%;
            --container-xl: 100%;
            --container-2xl: 100%;
            --container-3xl: 100%;
            --container-4xl: 100%;
            --container-5xl: 100%;
            --container-6xl: 100%;
            --container-7xl: 100%;
        }

        .wado-create-main [class^='fi-width-'],
        .wado-create-main [class*=' fi-width-'] {
            max-width: 100% !important;
            width: 100% !important;
            flex-basis: 100% !important;
        }

        .wado-create-main .fi-sc > .fi-grid-col,
        .wado-create-main .fi-sc > .fi-grid-col[class*='fi-width-'] {
            max-width: 100% !important;
            width: 100% !important;
            flex-basis: 100% !important;
        }

        .wado-create-main .fi-sc-component,
        .wado-create-main .fi-sc-component-ctn,
        .wado-create-main .fi-sc-tabs-tab {
            max-width: 100% !important;
            width: 100% !important;
        }

        .wado-create-main .fi-sc-tabs.fi-contained {
            border-radius: 14px;
            border: 1px solid #d9dde5;
            box-shadow: none;
        }

        .wado-create-main .fi-tabs.fi-contained {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 0;
            overflow: hidden;
            border-bottom: 1px solid #d9dde5;
            padding: 0;
            background: #fff;
            counter-reset: create-step;
        }

        .wado-create-main .fi-tabs-item {
            counter-increment: create-step;
            position: relative;
            border-radius: 0;
            border: 0;
            border-inline-end: 1px solid #d9dde5;
            justify-content: flex-start;
            gap: 0.55rem;
            padding: 0.85rem 1rem;
            white-space: normal;
            min-height: 46px;
            background: #fff;
        }

        .wado-create-main .fi-tabs-item:last-child {
            border-inline-end: 0;
        }

        .wado-create-main .fi-tabs-item > .fi-icon {
            display: none;
        }

        .wado-create-main .fi-tabs-item::before {
            content: counter(create-step);
            width: 1.2rem;
            height: 1.2rem;
            border-radius: 999px;
            background: #f2f4f8;
            color: #111827;
            font-size: 0.7rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            margin-top: 0.05rem;
        }

        .wado-create-main .fi-tabs-item .fi-tabs-item-label {
            color: #111827;
            font-size: 0.92rem;
            font-weight: 600;
            line-height: 1.2;
            white-space: normal;
        }

        .wado-create-main .fi-tabs-item.fi-active {
            background: #112657;
        }

        .wado-create-main .fi-tabs-item.fi-active::before {
            background: #2f4b86;
            color: #fff;
        }

        .wado-create-main .fi-tabs-item.fi-active .fi-tabs-item-label {
            color: #fff;
        }

        .wado-create-main .fi-sc-tabs-tab.fi-active {
            margin-top: 0;
            padding: 0;
            background: #fff;
        }

        .wado-create-main .fi-section {
            border-radius: 14px;
            border: 1px solid #d9dde5;
            box-shadow: none;
        }

        .wado-create-main .fi-section-header {
            border-bottom: 1px solid #e5e7eb;
            background: #fff;
        }

        .wado-create-main .fi-section-content {
            background: #fff;
        }

        .wado-create-aside {
            position: sticky;
            top: 1rem;
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin-left: 0;
            padding-left: 0;
        }

        .wado-side-card {
            border-radius: 14px;
            border: 1px solid #d9dde5;
            background: #fff;
            overflow: hidden;
        }

        .wado-side-title {
            margin: 0;
            padding: 0.95rem 1rem;
            border-bottom: 1px solid #e5e7eb;
            font-size: 1.05rem;
            font-weight: 700;
            color: #111827;
        }

        .wado-cover-preview {
            margin: 1rem;
            min-height: 140px;
            border: 1px dashed #c8cfdb;
            border-radius: 12px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 0.35rem;
            text-align: center;
            color: #64748b;
            font-size: 0.9rem;
            background: #f9fbff;
            overflow: hidden;
        }

        .wado-cover-preview small {
            font-size: 0.72rem;
            color: #94a3b8;
        }

        .wado-cover-preview img {
            width: 100%;
            height: 170px;
            object-fit: cover;
        }

        .wado-progress-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            padding: 1rem 1rem 0.45rem;
            color: #111827;
            font-size: 0.94rem;
        }

        .wado-progress-row strong {
            font-size: 0.95rem;
            font-weight: 700;
            color: #1f2937;
        }

        .wado-progress-track {
            height: 4px;
            margin: 0 1rem 1rem;
            border-radius: 99px;
            background: #e5e7eb;
            overflow: hidden;
        }

        .wado-progress-track span {
            display: block;
            height: 100%;
            width: 25%;
            background: #112657;
            border-radius: inherit;
            transition: width 0.2s ease;
        }

        .wado-step-list {
            list-style: none;
            padding: 0 1rem 1.1rem;
            margin: 0;
            display: grid;
            gap: 0.95rem;
            counter-reset: side-step;
        }

        .wado-step-list li {
            position: relative;
            counter-increment: side-step;
            padding-left: 2rem;
            color: #111827;
            font-size: 0.95rem;
        }

        .wado-step-list li::before {
            content: counter(side-step);
            position: absolute;
            left: 0;
            top: 0.1rem;
            width: 1.35rem;
            height: 1.35rem;
            border: 1px solid #c5cdd8;
            border-radius: 999px;
            color: #111827;
            background: #fff;
            font-size: 0.72rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .wado-step-list li:not(:last-child)::after {
            content: '';
            position: absolute;
            left: 0.64rem;
            top: 1.65rem;
            width: 1px;
            height: 1.05rem;
            background: #d1d5db;
        }

        .wado-step-list li.is-active {
            font-weight: 700;
        }

        .wado-step-list li.is-active::before {
            border-color: #112657;
            background: #112657;
            color: #fff;
        }

        .wado-tip-box {
            margin: 1rem;
            margin-top: 0;
            padding: 0.95rem;
            border-radius: 10px;
            background: #f8f8f6;
            border: 1px solid #ecebe8;
        }

        .wado-tip-box h4 {
            margin: 0 0 0.3rem;
            font-size: 0.99rem;
            font-weight: 700;
            color: #111827;
        }

        .wado-tip-box p {
            margin: 0;
            font-size: 0.95rem;
            line-height: 1.45;
            color: #374151;
        }

        @media (max-width: 1280px) {
            .wado-create-grid {
                grid-template-columns: minmax(0, 1fr) 300px;
            }

            .wado-create-main .fi-tabs.fi-contained {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 1024px) {
            .wado-create-grid {
                grid-template-columns: 1fr;
            }

            .wado-create-aside {
                position: static;
                order: 2;
            }
        }

        @media (max-width: 640px) {
            .wado-create-main .fi-tabs.fi-contained {
                grid-template-columns: 1fr;
            }

            .wado-progress-row,
            .wado-side-title,
            .wado-tip-box,
            .wado-cover-preview {
                margin-left: 0.85rem;
                margin-right: 0.85rem;
            }
        }
    </style>
</x-filament-panels::page>
