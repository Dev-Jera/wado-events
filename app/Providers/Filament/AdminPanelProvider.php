<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\AgentOverviewWidget;
use App\Filament\Widgets\QuickActionsWidget;
use App\Filament\Widgets\SuperAdminOverviewWidget;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('dashboard')
            ->login()
            ->databaseNotifications()
            ->maxContentWidth('full') // ← ADD THIS LINE - removes width constraint

            // ── Brand ──────────────────────────────────────
            ->brandName('WADO')
            ->brandLogo(asset('images/logos/logo-no-bg.png'))
            ->brandLogoHeight('2.2rem')

            // ── Colors ─────────────────────────────────────
            ->colors([
                'primary' => Color::hex('#f8b26a'),
                'gray'    => Color::Slate,
            ])

            // ── Dark navy sidebar ───────────────────────────
            ->renderHook('panels::head.end', fn () => '
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
@import url("https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&display=swap");

/* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
   LOGIN PAGE — RESPONSIVE
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */

/* Full-screen centred layout */
.fi-simple-layout {
    min-height: 100dvh !important;
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
    justify-content: center !important;
    background: #f0f4fb !important;
    padding: 1.25rem !important;
    box-sizing: border-box !important;
}

/* Card wrapper */
.fi-simple-main {
    width: 100% !important;
    max-width: 420px !important;
    background: #fff !important;
    border-radius: 18px !important;
    box-shadow: 0 4px 32px rgba(13,27,62,.10) !important;
    padding: 2rem 1.75rem !important;
    box-sizing: border-box !important;
}

/* Logo / brand area inside the card */
.fi-logo {
    display: flex !important;
    justify-content: center !important;
    margin-bottom: 1.5rem !important;
}
.fi-logo img {
    height: 2.8rem !important;
    width: auto !important;
}

/* Page heading ("Sign in") */
.fi-simple-page .fi-header-heading,
.fi-simple-page h1 {
    font-size: 1.45rem !important;
    font-weight: 800 !important;
    color: #0d1b3e !important;
    text-align: center !important;
    margin-bottom: 1.5rem !important;
}

/* Field labels */
.fi-simple-page .fi-fo-field-label-content {
    font-size: .7rem !important;
    font-weight: 700 !important;
    color: #374151 !important;
    text-transform: uppercase !important;
    letter-spacing: .06em !important;
}

/* Inputs — full width, taller touch targets */
.fi-simple-page .fi-input-wrp {
    border: 1.5px solid #dde4f0 !important;
    border-radius: 10px !important;
    background: #f8fafc !important;
    width: 100% !important;
}
.fi-simple-page .fi-input-wrp:focus-within {
    border-color: #0a4fbe !important;
    background: #fff !important;
    box-shadow: 0 0 0 3px rgba(10,79,190,.10) !important;
}
.fi-simple-page .fi-input {
    font-size: 1rem !important;
    padding-top: .7rem !important;
    padding-bottom: .7rem !important;
    width: 100% !important;
    min-height: 44px !important;
}

/* Remember me checkbox row */
.fi-simple-page .fi-checkbox-label,
.fi-simple-page [x-data] label {
    font-size: .82rem !important;
    color: #475569 !important;
}

/* Sign in button — full width, prominent */
.fi-simple-page .fi-btn-primary,
.fi-simple-page .fi-form-actions .fi-btn,
.fi-simple-page button[type="submit"] {
    width: 100% !important;
    justify-content: center !important;
    background: #0a4fbe !important;
    border-color: #0a4fbe !important;
    color: #fff !important;
    border-radius: 10px !important;
    font-weight: 700 !important;
    font-size: 1rem !important;
    padding-top: .75rem !important;
    padding-bottom: .75rem !important;
    min-height: 48px !important;
    margin-top: .5rem !important;
}
.fi-simple-page .fi-btn-primary:hover,
.fi-simple-page button[type="submit"]:hover {
    background: #083f98 !important;
    border-color: #083f98 !important;
}

/* Tighten spacing between fields */
.fi-simple-page .fi-fo-field-wrp {
    margin-bottom: 1rem !important;
}

/* Small screens — reduce card padding */
@media (max-width: 480px) {
    .fi-simple-layout {
        padding: .75rem !important;
        justify-content: flex-start !important;
        padding-top: 2rem !important;
    }
    .fi-simple-main {
        padding: 1.5rem 1.25rem !important;
        border-radius: 14px !important;
    }
}


/* Rounded dashboard font */
:root {
    --wado-admin-font: "Quicksand", "Nunito", "Plus Jakarta Sans", "Segoe UI", sans-serif;
    --font-family: var(--wado-admin-font) !important;
    --default-font-family: var(--wado-admin-font) !important;
}

html.fi {
    --font-family: var(--wado-admin-font) !important;
    --default-font-family: var(--wado-admin-font) !important;
}

.fi-body,
.fi-layout,
.fi-main,
.fi-main-ctn,
.fi-sidebar,
.fi-sidebar-nav,
.fi-header,
.fi-ta,
.fi-section,
.fi-wi,
.fi-btn,
.fi-input,
.fi-fo-field-wrp,
.fi-modal,
.fi-dropdown {
    font-family: var(--wado-admin-font) !important;
}

/* App shell */
.fi-body,
.fi-main,
.fi-main-ctn,
.fi-layout {
    background: #eef2f8 !important;
}

/* Sidebar background */
.fi-sidebar,
.fi-sidebar-nav,
nav.fi-sidebar-nav {
    background: #f7f9fd !important;
    border-right: 1px solid #dfe7f3 !important;
}

/* Sidebar header / brand area */
.fi-sidebar-header {
    background: #f7f9fd !important;
    border-bottom: 1px solid #e7edf7 !important;
}

/* Nav group labels */
.fi-sidebar-group-label {
    color: #7f90ab !important;
    font-size: .62rem !important;
    letter-spacing: .08em !important;
    font-weight: 700 !important;
}

/* Nav items — default */
.fi-sidebar-item-button {
    color: #2a3f63 !important;
    border-radius: 10px !important;
    min-height: 2.2rem !important;
    padding-inline: .62rem !important;
}
.fi-sidebar-item-button svg,
.fi-sidebar-item-button span,
.fi-sidebar-item-label {
    color: #2a3f63 !important;
}
.fi-sidebar-item-button:hover {
    background: #edf3ff !important;
    color: #1f4faa !important;
}
.fi-sidebar-item-button:hover svg,
.fi-sidebar-item-button:hover span,
.fi-sidebar-item-button:hover .fi-sidebar-item-label {
    color: #1f4faa !important;
}

/* Nav items — active */
.fi-sidebar-item-button.fi-active,
.fi-sidebar-item-button[aria-current],
.fi-sidebar-item-button[aria-current="page"] {
    background: #e8f0ff !important;
    color: #1e4ea8 !important;
    box-shadow: inset 0 0 0 1px #cfdcf8 !important;
}
.fi-sidebar-item-button.fi-active svg,
.fi-sidebar-item-button.fi-active span,
.fi-sidebar-item-button.fi-active .fi-sidebar-item-label,
.fi-sidebar-item-button[aria-current] svg,
.fi-sidebar-item-button[aria-current] span,
.fi-sidebar-item-button[aria-current] .fi-sidebar-item-label {
    color: #1e4ea8 !important;
}

/* Active state fallback selectors across Filament variants */
.fi-sidebar .fi-sidebar-item.fi-active .fi-sidebar-item-button,
.fi-sidebar .fi-sidebar-item[aria-current="page"] .fi-sidebar-item-button,
.fi-sidebar .fi-sidebar-item.fi-active .fi-sidebar-item-label,
.fi-sidebar .fi-sidebar-item[aria-current="page"] .fi-sidebar-item-label,
.fi-sidebar .fi-sidebar-item.fi-active svg,
.fi-sidebar .fi-sidebar-item[aria-current="page"] svg {
    color: #1e4ea8 !important;
}

/* Sidebar footer */
.fi-sidebar-footer {
    background: #f7f9fd !important;
    border-top: 1px solid #e7edf7 !important;
}

.wado-sidebar-logout {
    width: 100%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .35rem;
    padding: .5rem .7rem;
    border-radius: 10px;
    border: 1px solid #d7e1ef;
    background: #ffffff;
    color: #30486f;
    font-size: .69rem;
    font-weight: 700;
    line-height: 1;
    cursor: pointer;
    transition: background .15s, border-color .15s;
}
.wado-sidebar-logout:hover {
    background: #edf3ff;
    border-color: #c5d7f6;
}

.wado-sidebar-foot {
    border: 1px solid #dbe4f0;
    border-radius: 12px;
    background: #ffffff;
    padding: .65rem;
    display: grid;
    gap: .45rem;
}
.wado-sidebar-user {
    display: grid;
    gap: .1rem;
}
.wado-sidebar-user strong {
    color: #1b2e4d;
    font-size: .69rem;
    font-weight: 700;
    line-height: 1.2;
}
.wado-sidebar-user span {
    color: #7b8da8;
    font-size: .63rem;
    font-weight: 600;
}

/* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
   CREATE / EDIT EVENT FORM
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */

/* ── Page header — tighter spacing & smaller headings ── */
.fi-header {
    padding-top: .6rem !important;
    padding-bottom: .6rem !important;
    margin-bottom: .25rem !important;
}
.fi-header-heading {
    font-size: 1.2rem !important;
    font-weight: 800 !important;
    line-height: 1.3 !important;
    color: #0d1b3e !important;
    margin: 0 !important;
}
.fi-header-subheading {
    font-size: .78rem !important;
    color: #64748b !important;
    margin-top: .15rem !important;
}

/* ── Pull all page content up ── */
.fi-page-header-main-ctn {
    padding-block: .75rem !important;
    row-gap: .75rem !important;
}
.fi-page-content {
    gap: 1.25rem !important;
    row-gap: 1.25rem !important;
}

/* ── Section cards ── */
.fi-section {
    border-radius: 12px !important;
    border: 1px solid #e2e8f0 !important;
    box-shadow: 0 1px 4px rgba(13,27,62,.06) !important;
    background: #fff !important;
    overflow: hidden !important;
}
.fi-section-header {
    background: #f8fafc !important;
    padding: .8rem 1.25rem !important;
    border-bottom: 1px solid #e2e8f0 !important;
}
.fi-section-header-heading {
    color: #0f172a !important;
    font-size: .88rem !important;
    font-weight: 700 !important;
}
.fi-section-header-description {
    color: #64748b !important;
    font-size: .72rem !important;
}
.fi-section-header svg { color: #475569 !important; }
.fi-section-content-ctn { padding: 1.25rem !important; }

/* ── Field labels ── */
.fi-fo-field-label-content {
    font-size: .72rem !important;
    font-weight: 700 !important;
    color: #374151 !important;
    text-transform: uppercase !important;
    letter-spacing: .05em !important;
}
.fi-fo-field-label-required-mark { color: #c8102e !important; }

/* ── Inputs ── */
.fi-input-wrp {
    border: 1.5px solid #e2e8f0 !important;
    border-radius: 8px !important;
    background: #f9fafb !important;
    transition: border-color .15s, box-shadow .15s, background .15s !important;
}
.fi-input-wrp:focus-within {
    border-color: #0d1b3e !important;
    background: #fff !important;
    box-shadow: 0 0 0 3px rgba(13,27,62,.08) !important;
}
.fi-input {
    font-size: .875rem !important;
    color: #111827 !important;
    padding-top: .55rem !important;
    padding-bottom: .55rem !important;
}
textarea.fi-fo-textarea {
    border: 1.5px solid #e2e8f0 !important;
    border-radius: 8px !important;
    background: #f9fafb !important;
    font-size: .875rem !important;
    color: #111827 !important;
    padding: .6rem .85rem !important;
    width: 100% !important;
}
textarea.fi-fo-textarea:focus {
    border-color: #0d1b3e !important;
    background: #fff !important;
    outline: none !important;
    box-shadow: 0 0 0 3px rgba(13,27,62,.08) !important;
}
.fi-input-wrp-prefix {
    font-size: .78rem !important;
    font-weight: 600 !important;
    color: #9ca3af !important;
    border-right: 1.5px solid #e2e8f0 !important;
    padding: 0 .75rem !important;
}

/* ── Repeater ── */
.fi-fo-repeater-item {
    border: 1px solid #e2e8f0 !important;
    border-radius: 10px !important;
    overflow: hidden !important;
    background: #fff !important;
    margin-bottom: .6rem !important;
}
.fi-fo-repeater-item-header {
    background: #f3f4f6 !important;
    border-bottom: 1px solid #e5e7eb !important;
    padding: .5rem .85rem !important;
}
.fi-fo-repeater-item-label {
    font-size: .78rem !important;
    font-weight: 700 !important;
    color: #0d1b3e !important;
}
.fi-fo-repeater-item-content { padding: .85rem !important; }
.fi-fo-repeater-add-action button,
.fi-fo-repeater-add-action .fi-btn {
    width: 100% !important;
    justify-content: center !important;
    border: 1.5px dashed #d1d5db !important;
    border-radius: 8px !important;
    background: #fff !important;
    color: #6b7280 !important;
    font-size: .78rem !important;
    font-weight: 600 !important;
    padding: .55rem 1rem !important;
}
.fi-fo-repeater-add-action button:hover,
.fi-fo-repeater-add-action .fi-btn:hover {
    border-color: #0d1b3e !important;
    color: #0d1b3e !important;
    background: #f0f4fb !important;
}

/* ── Stat placeholders ── */
.fi-fo-placeholder {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    padding: .45rem 0 !important;
    border-bottom: 1px solid #f3f4f6 !important;
}
.fi-fo-placeholder:last-child { border-bottom: none !important; }
.fi-fo-placeholder-label {
    font-size: .7rem !important;
    color: #6b7280 !important;
    text-transform: uppercase !important;
    letter-spacing: .04em !important;
}
.fi-fo-placeholder-content {
    font-size: .82rem !important;
    font-weight: 700 !important;
    color: #0d1b3e !important;
}

/* ── Buttons ── */
.fi-btn-primary,
.fi-btn-color-primary,
.fi-ac-btn-action {
    background: #0a4fbe !important;
    border-color: #0a4fbe !important;
    color: #ffffff !important;
    border-radius: 8px !important;
    font-weight: 700 !important;
}
.fi-btn-primary:hover,
.fi-btn-color-primary:hover,
.fi-ac-btn-action:hover {
    background: #083f98 !important;
    border-color: #083f98 !important;
}
.fi-btn-secondary, .fi-btn-gray { border-radius: 8px !important; }
</style>
')

            ->renderHook('panels::sidebar.footer', fn () => '
<div class="px-3 pb-3">
    <div class="wado-sidebar-foot">
        <div class="wado-sidebar-user">
            <strong>' . e(auth()->user()?->name ?? 'Operator') . '</strong>
            <span>' . e(str_replace('_', ' ', (string) (auth()->user()?->role ?? 'team'))) . '</span>
        </div>
        <form method="POST" action="' . route('filament.admin.auth.logout') . '">
            ' . csrf_field() . '
            <button type="submit" class="wado-sidebar-logout">Log out</button>
        </form>
    </div>
</div>
')

            // ── Resources & pages ──────────────────────────
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([Dashboard::class])

            // ── Widgets ────────────────────────────────────
            ->widgets([
                QuickActionsWidget::class,
                AgentOverviewWidget::class,
                SuperAdminOverviewWidget::class,
            ])

            // ── Navigation groups ──────────────────────────
            ->navigationGroups([
                NavigationGroup::make('Events'),
                NavigationGroup::make('Operations'),
                NavigationGroup::make('Logs'),
            ])

            // ── Middleware ─────────────────────────────────
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}