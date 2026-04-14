<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\QuickActionsWidget;
use App\Filament\Widgets\SuperAdminOverviewWidget;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
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

            // ── Brand ──────────────────────────────────────
            ->brandName('WADO')
            ->brandLogo(asset('images/wado-logo.png'))
            ->brandLogoHeight('2.2rem')

            // ── Colors ─────────────────────────────────────
            ->colors([
                'primary' => Color::hex('#f8b26a'),
                'gray'    => Color::Slate,
            ])

            // ── Dark navy sidebar ───────────────────────────
            ->renderHook('panels::head.end', fn () => '
<style>
/* Sidebar background */
.fi-sidebar,
.fi-sidebar-nav,
nav.fi-sidebar-nav {
    background-color: #0d1b3e !important;
}

/* Sidebar header / brand area */
.fi-sidebar-header {
    background-color: #0d1b3e !important;
    border-bottom-color: rgba(255,255,255,.08) !important;
}

/* Nav group labels */
.fi-sidebar-group-label {
    color: rgba(255,255,255,.4) !important;
    font-size: .62rem !important;
    letter-spacing: .08em !important;
}

/* Nav items — default */
.fi-sidebar-item-button {
    color: #fff !important;
    border-radius: 8px !important;
}
.fi-sidebar-item-button:hover {
    background-color: rgba(255,255,255,.1) !important;
    color: #93c5fd !important;
}
.fi-sidebar-item-button:hover svg,
.fi-sidebar-item-button:hover span,
.fi-sidebar-item-button:hover .fi-sidebar-item-label {
    color: #93c5fd !important;
}

/* Nav items — active */
.fi-sidebar-item-button.fi-active,
.fi-sidebar-item-button[aria-current],
.fi-sidebar-item-button[aria-current="page"] {
    background-color: #c8102e !important;
    color: #fff !important;
}
.fi-sidebar-item-button.fi-active svg,
.fi-sidebar-item-button.fi-active span,
.fi-sidebar-item-button.fi-active .fi-sidebar-item-label,
.fi-sidebar-item-button[aria-current] svg,
.fi-sidebar-item-button[aria-current] span,
.fi-sidebar-item-button[aria-current] .fi-sidebar-item-label {
    color: #fff !important;
}

/* Nav item icons — always white */
.fi-sidebar-item-button svg,
.fi-sidebar-item-button span,
.fi-sidebar-item-label {
    color: #fff !important;
}

/* Sidebar footer / user menu area */
.fi-sidebar-footer {
    background-color: #0a1530 !important;
    border-top-color: rgba(255,255,255,.08) !important;
}
.fi-sidebar-footer,
.fi-sidebar-footer * {
    color: #fff !important;
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
.fi-page-main {
    padding-top: .5rem !important;
}
.fi-page-header-main-ctn {
    padding-top: .5rem !important;
    padding-bottom: .25rem !important;
}

/* ── Section card ── */
.ef-section {
    border-radius: 14px !important;
    border: 1px solid #e8edf5 !important;
    box-shadow: 0 1px 3px rgba(13,27,62,.05) !important;
    overflow: hidden !important;
    background: #fff !important;
}

/* ── Section header — white with icon badge ── */
.ef-section .fi-section-header {
    background: #fff !important;
    padding: .9rem 1.25rem !important;
    border-bottom: 1px solid #f1f5f9 !important;
    display: flex !important;
    align-items: center !important;
    gap: .7rem !important;
}
.ef-section .fi-section-header-heading,
.ef-section .fi-section-header .fi-heading {
    color: #0d1b3e !important;
    font-size: .9rem !important;
    font-weight: 700 !important;
}
.ef-section .fi-section-header-description {
    color: #94a3b8 !important;
    font-size: .72rem !important;
    margin-top: .1rem !important;
}
.ef-section .fi-section-header > svg,
.ef-section .fi-section-header .fi-icon {
    background: #f1f5f9 !important;
    color: #0d1b3e !important;
    padding: .35rem !important;
    border-radius: 8px !important;
    width: 32px !important;
    height: 32px !important;
    flex-shrink: 0 !important;
}

/* ── Section body ── */
.ef-section .fi-section-content-ctn {
    padding: 1.25rem !important;
    background: #fff !important;
}

/* ── Sidebar sections — slightly tighter ── */
.ef-sidebar-section .fi-section-content-ctn {
    padding: 1rem !important;
}

/* ── At a glance — row list ── */
.ef-glance-section .fi-fo-placeholder {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    padding: .5rem 0 !important;
    border-bottom: 1px solid #f1f5f9 !important;
}
.ef-glance-section .fi-fo-placeholder:last-child {
    border-bottom: none !important;
}
.ef-glance-section .fi-fo-placeholder-label {
    font-size: .78rem !important;
    color: #64748b !important;
    font-weight: 500 !important;
}
.ef-glance-section .fi-fo-placeholder-content {
    font-size: .82rem !important;
    font-weight: 700 !important;
    color: #0d1b3e !important;
}

/* ── Field labels ── */
.fi-fo-field-wrp-label label,
.fi-label-wrapper label {
    font-size: .72rem !important;
    font-weight: 700 !important;
    color: #475569 !important;
    letter-spacing: .04em !important;
    text-transform: uppercase !important;
}
.fi-fo-field-wrp-label .fi-required-indicator {
    color: #c8102e !important;
}

/* ── Inputs ── */
.fi-input-wrp {
    border: 1.5px solid #e2e8f0 !important;
    border-radius: 9px !important;
    background: #fff !important;
    transition: border-color .15s, box-shadow .15s !important;
}
.fi-input-wrp:focus-within {
    border-color: #0d1b3e !important;
    box-shadow: 0 0 0 3px rgba(13,27,62,.07) !important;
}
.fi-input {
    font-size: .875rem !important;
    color: #0d1b3e !important;
    background: transparent !important;
}
.fi-fo-textarea {
    border: 1.5px solid #e2e8f0 !important;
    border-radius: 9px !important;
    font-size: .875rem !important;
    color: #0d1b3e !important;
    line-height: 1.6 !important;
}
.fi-fo-textarea:focus {
    border-color: #0d1b3e !important;
    box-shadow: 0 0 0 3px rgba(13,27,62,.07) !important;
    outline: none !important;
}

/* ── Input prefix (UGX) ── */
.fi-input-wrp-prefix {
    color: #94a3b8 !important;
    font-size: .8rem !important;
    font-weight: 600 !important;
    border-right-color: #e2e8f0 !important;
    padding-left: .85rem !important;
}

/* ── Select ── */
.fi-select-input {
    border: 1.5px solid #e2e8f0 !important;
    border-radius: 9px !important;
    font-size: .875rem !important;
    color: #0d1b3e !important;
}
.fi-select-input:focus {
    border-color: #0d1b3e !important;
    box-shadow: 0 0 0 3px rgba(13,27,62,.07) !important;
    outline: none !important;
}

/* ── Helper text ── */
.fi-fo-field-wrp-hint {
    color: #94a3b8 !important;
    font-size: .7rem !important;
}

/* ── Toggle rows ── */
.fi-fo-toggle {
    padding: .7rem 0 !important;
    border-bottom: 1px solid #f1f5f9 !important;
}
.fi-fo-toggle:last-child { border-bottom: none !important; }
.fi-fo-toggle-label {
    font-size: .82rem !important;
    font-weight: 600 !important;
    color: #0d1b3e !important;
    text-transform: none !important;
    letter-spacing: 0 !important;
}

/* ── File upload zone ── */
.fi-fo-file-upload {
    border: 2px dashed #cbd5e1 !important;
    border-radius: 10px !important;
    background: #f8fafc !important;
}
.fi-fo-file-upload:hover {
    border-color: #0d1b3e !important;
    background: #f0f4fb !important;
}

/* ── Repeater items ── */
.fi-fo-repeater-item {
    border: 1.5px solid #e2e8f0 !important;
    border-radius: 10px !important;
    background: #fff !important;
    overflow: hidden !important;
    margin-bottom: .6rem !important;
}
.fi-fo-repeater-item-header {
    background: #f8fafc !important;
    border-bottom: 1px solid #e8edf5 !important;
    padding: .5rem 1rem !important;
}
.fi-fo-repeater-item-header-label {
    font-size: .78rem !important;
    font-weight: 700 !important;
    color: #0d1b3e !important;
}
.fi-fo-repeater-item-content {
    padding: 1rem !important;
}

/* Numbered badge on repeater items */
.fi-fo-repeater-item-header-label::before {
    content: counter(repeater-item);
    counter-increment: repeater-item;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 20px;
    height: 20px;
    background: #0d1b3e;
    color: #fff;
    border-radius: 50%;
    font-size: .65rem;
    font-weight: 800;
    margin-right: .5rem;
}
.fi-fo-repeater-list { counter-reset: repeater-item; }

/* ── Add item button ── */
.fi-fo-repeater-add-action .fi-btn {
    width: 100% !important;
    justify-content: center !important;
    border: 1.5px solid #e2e8f0 !important;
    border-radius: 9px !important;
    background: #fff !important;
    color: #475569 !important;
    font-size: .78rem !important;
    font-weight: 600 !important;
    padding: .6rem 1rem !important;
    transition: all .15s !important;
}
.fi-fo-repeater-add-action .fi-btn:hover {
    border-color: #0d1b3e !important;
    color: #0d1b3e !important;
    background: #f0f4fb !important;
}

/* ── Save / Create button ── */
.fi-btn-primary {
    background: #0d1b3e !important;
    border-color: #0d1b3e !important;
    border-radius: 9px !important;
    font-weight: 700 !important;
    font-size: .875rem !important;
    transition: background .15s !important;
}
.fi-btn-primary:hover {
    background: #c8102e !important;
    border-color: #c8102e !important;
}

/* ── Cancel button ── */
.fi-btn-secondary, .fi-btn-gray {
    border-radius: 9px !important;
    font-weight: 600 !important;
}
</style>
')

            // ── Resources & pages ──────────────────────────
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([Dashboard::class])

            // ── Widgets ────────────────────────────────────
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                QuickActionsWidget::class,
                SuperAdminOverviewWidget::class,
            ])

            // ── Navigation groups ──────────────────────────
            ->navigationGroups([
                NavigationGroup::make('Events'),
                NavigationGroup::make('Operations')
                    ->collapsed(),
            ])

            // ── Extra nav items ────────────────────────────
            ->navigationItems([
                NavigationItem::make('Gate Scanner')
                    ->url(fn () => route('tickets.verify.index'))
                    ->icon('heroicon-o-qr-code')
                    ->group('Operations')
                    ->sort(5)
                    ->isActiveWhen(fn () => request()->routeIs('tickets.verify.*')),

                NavigationItem::make('Users & Agents')
                    ->url(fn () => route('admin.users.index'))
                    ->icon('heroicon-o-users')
                    ->group('Operations')
                    ->sort(15)
                    ->isActiveWhen(fn () => request()->routeIs('admin.users.*')),
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
