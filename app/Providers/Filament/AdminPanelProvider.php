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
            ->databaseNotifications()
            ->maxContentWidth('full') // ← ADD THIS LINE - removes width constraint

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
    background-color: #e2e8f0 !important;
    color: #0f172a !important;
}
.fi-sidebar-item-button.fi-active svg,
.fi-sidebar-item-button.fi-active span,
.fi-sidebar-item-button.fi-active .fi-sidebar-item-label,
.fi-sidebar-item-button[aria-current] svg,
.fi-sidebar-item-button[aria-current] span,
.fi-sidebar-item-button[aria-current] .fi-sidebar-item-label {
    color: #0f172a !important;
}

/* Active state fallback selectors across Filament variants */
.fi-sidebar .fi-sidebar-item.fi-active .fi-sidebar-item-button,
.fi-sidebar .fi-sidebar-item[aria-current="page"] .fi-sidebar-item-button,
.fi-sidebar .fi-sidebar-item.fi-active .fi-sidebar-item-label,
.fi-sidebar .fi-sidebar-item[aria-current="page"] .fi-sidebar-item-label,
.fi-sidebar .fi-sidebar-item.fi-active svg,
.fi-sidebar .fi-sidebar-item[aria-current="page"] svg {
    color: #0f172a !important;
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

.wado-sidebar-logout {
    width: auto;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: .35rem .62rem;
    border-radius: 6px;
    border: 1px solid #c8102e;
    background: #c8102e;
    color: #fff;
    font-size: .69rem;
    font-weight: 700;
    line-height: 1;
    cursor: pointer;
    transition: opacity .15s;
}
.wado-sidebar-logout:hover {
    opacity: .9;
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
.fi-btn-primary {
    background: #0d1b3e !important;
    border-color: #0d1b3e !important;
    border-radius: 8px !important;
    font-weight: 700 !important;
}
.fi-btn-primary:hover {
    background: #c8102e !important;
    border-color: #c8102e !important;
}
.fi-btn-secondary, .fi-btn-gray { border-radius: 8px !important; }
</style>
')

            ->renderHook('panels::sidebar.footer', fn () => '
<div class="px-3 pb-3">
    <form method="POST" action="' . route('filament.admin.auth.logout') . '">
        ' . csrf_field() . '
        <button type="submit" class="wado-sidebar-logout">Log out</button>
    </form>
</div>
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