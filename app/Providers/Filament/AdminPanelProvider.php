<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\AgentOverviewWidget;
use App\Filament\Widgets\QuickActionsWidget;
use App\Filament\Widgets\SuperAdminOverviewWidget;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
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
            ->authGuard('admin')
            ->login()
            ->databaseNotifications()
            ->maxContentWidth('full')

            // ── Brand ──────────────────────────────────────
            ->brandName('WADO')
            ->brandLogo(asset('images/logos/Wado Ticketing.png'))
            ->brandLogoHeight('3.1rem')

            // ── Colors ─────────────────────────────────────
            ->colors([
                'primary' => Color::hex('#2563eb'),
                'gray'    => Color::Slate,
            ])

            // ── Styles injected into admin <head> ───────────
            ->renderHook('panels::head.end', fn () => '
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
@import url("https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&display=swap");

/* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
   LOGIN PAGE — RESPONSIVE
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */

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
.fi-simple-main {
    width: 100% !important;
    max-width: 420px !important;
    background: #fff !important;
    border-radius: 18px !important;
    box-shadow: 0 4px 32px rgba(13,27,62,.10) !important;
    padding: 2rem 1.75rem !important;
    box-sizing: border-box !important;
}
.fi-logo {
    display: flex !important;
    justify-content: center !important;
    margin-bottom: 1.5rem !important;
}
.fi-logo img {
    height: 3.1rem !important;
    width: auto !important;
}
.fi-simple-page .fi-header-heading,
.fi-simple-page h1 {
    font-size: 1.45rem !important;
    font-weight: 800 !important;
    color: #0d1b3e !important;
    text-align: center !important;
    margin-bottom: 1.5rem !important;
}
.fi-simple-page .fi-fo-field-label-content {
    font-size: .7rem !important;
    font-weight: 700 !important;
    color: #374151 !important;
    text-transform: uppercase !important;
    letter-spacing: .06em !important;
}
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
.fi-simple-page .fi-checkbox-label,
.fi-simple-page [x-data] label {
    font-size: .82rem !important;
    color: #475569 !important;
}
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
.fi-simple-page .fi-fo-field-wrp {
    margin-bottom: 1rem !important;
}
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

/* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
   FONTS & BASE
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */

:root {
    --wado-admin-font: "Quicksand", "Nunito", "Plus Jakarta Sans", "Segoe UI", sans-serif;
}
html.fi { --font-family: var(--wado-admin-font) !important; }
.fi-body, .fi-layout, .fi-main, .fi-main-ctn, .fi-sidebar, .fi-sidebar-nav,
.fi-header, .fi-ta, .fi-section, .fi-wi, .fi-btn, .fi-input,
.fi-fo-field-wrp, .fi-modal, .fi-dropdown {
    font-family: var(--wado-admin-font) !important;
}

/* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
   APP SHELL
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */

.fi-body, .fi-main, .fi-main-ctn, .fi-layout { background: #f0f2f8 !important; }

/* ── Sidebar base ── */
.fi-sidebar, .fi-sidebar-nav, nav.fi-sidebar-nav {
    background: #ffffff !important;
    border-right: 1px solid #e2e8f0 !important;
}
.fi-sidebar-header {
    background: #ffffff !important;
    border-bottom: 1px solid #e2e8f0 !important;
}

/* ── Section group separators ── */
.fi-sidebar-group,
.fi-sidebar-nav ul > li.fi-sidebar-group,
[class*="fi-sidebar-group"] {
    border-top: 1px solid #dde3ed !important;
    margin-top: .5rem !important;
    padding-top: .5rem !important;
}
.fi-sidebar-group:first-child,
.fi-sidebar-nav ul > li.fi-sidebar-group:first-child,
[class*="fi-sidebar-group"]:first-child {
    border-top: none !important;
    margin-top: 0 !important;
    padding-top: 0 !important;
}
/* fallback: separator line above each group label */
.fi-sidebar-group-label {
    position: relative !important;
}
.fi-sidebar-group + .fi-sidebar-group > .fi-sidebar-group-label::before,
.fi-sidebar-group ~ .fi-sidebar-group > .fi-sidebar-group-label::before {
    content: "" !important;
    display: block !important;
    height: 1px !important;
    background: #dde3ed !important;
    margin-bottom: .5rem !important;
}

/* ── Section labels ── */
.fi-sidebar-group-label {
    color: #2563eb !important;
    font-size: .6rem !important;
    letter-spacing: .12em !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
}

/* ── Nav items (correct class: fi-sidebar-item-btn on the <a>) ── */
.fi-sidebar-item-btn {
    color: #374151 !important;
    border-radius: 10px !important;
    min-height: 2.5rem !important;
    padding-inline: .7rem !important;
    transition: background .15s, color .15s !important;
    text-decoration: none !important;
}
.fi-sidebar-item-btn .fi-sidebar-item-icon,
.fi-sidebar-item-btn svg { color: #2563eb !important; }
.fi-sidebar-item-btn .fi-sidebar-item-label { color: #374151 !important; }

.fi-sidebar-item-btn:hover {
    background: #eff6ff !important;
}
.fi-sidebar-item-btn:hover .fi-sidebar-item-icon,
.fi-sidebar-item-btn:hover svg { color: #1d4ed8 !important; }
.fi-sidebar-item-btn:hover .fi-sidebar-item-label { color: #1d4ed8 !important; }

/* ── Parent with active child — tinted but NOT blue pill ── */
li.fi-sidebar-item-has-active-child-items > .fi-sidebar-item-btn {
    background: #eff6ff !important;
    color: #1d4ed8 !important;
}
li.fi-sidebar-item-has-active-child-items > .fi-sidebar-item-btn .fi-sidebar-item-icon,
li.fi-sidebar-item-has-active-child-items > .fi-sidebar-item-btn svg { color: #1d4ed8 !important; }
li.fi-sidebar-item-has-active-child-items > .fi-sidebar-item-btn .fi-sidebar-item-label { color: #1d4ed8 !important; }

/* ── Sub-items container ── */
.fi-sidebar-sub-group-items {
    background: #f8fafc !important;
    border-radius: 10px !important;
    margin-top: 2px !important;
    padding: 3px !important;
}
.fi-sidebar-sub-group-items .fi-sidebar-item-btn {
    color: #374151 !important;
    background: transparent !important;
}
.fi-sidebar-sub-group-items .fi-sidebar-item-btn .fi-sidebar-item-icon,
.fi-sidebar-sub-group-items .fi-sidebar-item-btn svg { color: #2563eb !important; }
.fi-sidebar-sub-group-items .fi-sidebar-item-btn .fi-sidebar-item-label { color: #374151 !important; }
.fi-sidebar-sub-group-items .fi-sidebar-item-btn:hover { background: #eff6ff !important; }
.fi-sidebar-sub-group-items .fi-sidebar-item-btn:hover .fi-sidebar-item-label,
.fi-sidebar-sub-group-items .fi-sidebar-item-btn:hover svg { color: #1d4ed8 !important; }

/* ── Active item — blue pill ── */
li.fi-active > .fi-sidebar-item-btn {
    background: #2563eb !important;
    color: #fff !important;
    box-shadow: 0 4px 16px rgba(37,99,235,.4) !important;
}
li.fi-active > .fi-sidebar-item-btn .fi-sidebar-item-icon,
li.fi-active > .fi-sidebar-item-btn svg { color: #fff !important; }
li.fi-active > .fi-sidebar-item-btn .fi-sidebar-item-label { color: #fff !important; }

/* ── Sidebar footer ── */
.fi-sidebar-footer {
    background: #ffffff !important;
    border-top: 1px solid #e2e8f0 !important;
}

/* ── Footer user card ── */
.wado-sidebar-foot {
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    background: #f8fafc;
    padding: .65rem;
    display: grid;
    gap: .5rem;
}
.wado-sidebar-user {
    display: flex;
    align-items: center;
    gap: .6rem;
}
.wado-sidebar-avatar {
    width: 34px; height: 34px;
    border-radius: 50%;
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    color: #fff;
    display: flex; align-items: center; justify-content: center;
    font-size: .78rem; font-weight: 800;
    flex-shrink: 0;
    box-shadow: 0 2px 8px rgba(37,99,235,.3);
}
.wado-sidebar-user-info { display: grid; gap: .1rem; overflow: hidden; }
.wado-sidebar-user-info strong {
    color: #0f172a; font-size: .72rem; font-weight: 700;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.wado-sidebar-user-info span { color: #94a3b8; font-size: .62rem; }
.wado-sidebar-logout {
    width: 100%;
    display: inline-flex; align-items: center; justify-content: center;
    gap: .4rem; padding: .48rem .7rem; border-radius: 8px;
    border: 1px solid #e2e8f0;
    background: #fff;
    color: #64748b;
    font-size: .69rem; font-weight: 700; line-height: 1; cursor: pointer;
    transition: background .15s, color .15s;
    font-family: inherit;
}
.wado-sidebar-logout:hover { background: #fef2f2; color: #dc2626; border-color: #fecaca; }

/* ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
   CREATE / EDIT EVENT FORM
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ */

.fi-header { padding-top: .6rem !important; padding-bottom: .6rem !important; margin-bottom: .25rem !important; }
.fi-header-heading { font-size: 1.2rem !important; font-weight: 800 !important; line-height: 1.3 !important; color: #0d1b3e !important; margin: 0 !important; }
.fi-header-subheading { font-size: .78rem !important; color: #64748b !important; margin-top: .15rem !important; }
.fi-page-header-main-ctn { padding-block: .75rem !important; row-gap: .75rem !important; }
.fi-page-content { gap: 1.25rem !important; row-gap: 1.25rem !important; }
.fi-section {
    border-radius: 12px !important; border: 1px solid #e2e8f0 !important;
    box-shadow: 0 1px 4px rgba(13,27,62,.06) !important;
    background: #fff !important; overflow: hidden !important;
}
.fi-section-header { background: #f8fafc !important; padding: .8rem 1.25rem !important; border-bottom: 1px solid #e2e8f0 !important; }
.fi-section-header-heading { color: #0f172a !important; font-size: .88rem !important; font-weight: 700 !important; }
.fi-section-header-description { color: #64748b !important; font-size: .72rem !important; }
.fi-section-header svg { color: #475569 !important; }
.fi-section-content-ctn { padding: 1.25rem !important; }
.fi-fo-field-label-content { font-size: .72rem !important; font-weight: 700 !important; color: #374151 !important; text-transform: uppercase !important; letter-spacing: .05em !important; }
.fi-fo-field-label-required-mark { color: #c8102e !important; }
.fi-input-wrp {
    border: 1.5px solid #e2e8f0 !important; border-radius: 8px !important;
    background: #f9fafb !important;
    transition: border-color .15s, box-shadow .15s, background .15s !important;
}
.fi-input-wrp:focus-within { border-color: #0d1b3e !important; background: #fff !important; box-shadow: 0 0 0 3px rgba(13,27,62,.08) !important; }
.fi-input { font-size: .875rem !important; color: #111827 !important; padding-top: .55rem !important; padding-bottom: .55rem !important; }
textarea.fi-fo-textarea {
    border: 1.5px solid #e2e8f0 !important; border-radius: 8px !important;
    background: #f9fafb !important; font-size: .875rem !important;
    color: #111827 !important; padding: .6rem .85rem !important; width: 100% !important;
}
textarea.fi-fo-textarea:focus { border-color: #0d1b3e !important; background: #fff !important; outline: none !important; box-shadow: 0 0 0 3px rgba(13,27,62,.08) !important; }
.fi-input-wrp-prefix { font-size: .78rem !important; font-weight: 600 !important; color: #9ca3af !important; border-right: 1.5px solid #e2e8f0 !important; padding: 0 .75rem !important; }
.fi-fo-repeater-item { border: 1px solid #e2e8f0 !important; border-radius: 10px !important; overflow: hidden !important; background: #fff !important; margin-bottom: .6rem !important; }
.fi-fo-repeater-item-header { background: #f3f4f6 !important; border-bottom: 1px solid #e5e7eb !important; padding: .5rem .85rem !important; }
.fi-fo-repeater-item-label { font-size: .78rem !important; font-weight: 700 !important; color: #0d1b3e !important; }
.fi-fo-repeater-item-content { padding: .85rem !important; }
.fi-fo-repeater-add-action button,
.fi-fo-repeater-add-action .fi-btn {
    width: 100% !important; justify-content: center !important;
    border: 1.5px dashed #d1d5db !important; border-radius: 8px !important;
    background: #fff !important; color: #6b7280 !important;
    font-size: .78rem !important; font-weight: 600 !important; padding: .55rem 1rem !important;
}
.fi-fo-repeater-add-action button:hover,
.fi-fo-repeater-add-action .fi-btn:hover { border-color: #0d1b3e !important; color: #0d1b3e !important; background: #f0f4fb !important; }
</style>
')

            // ── Sidebar footer: user info + logout ────────
            ->renderHook('panels::sidebar.footer', function () {
                $user   = auth()->user();
                $initials = $user ? strtoupper(mb_substr($user->name, 0, 1)) : '?';
                $role   = $user?->roles?->first()?->name ?? 'Admin';
                return '
<div style="padding:.5rem .75rem .8rem">
    <div class="wado-sidebar-foot">
        <div class="wado-sidebar-user">
            <div class="wado-sidebar-avatar">' . e($initials) . '</div>
            <div class="wado-sidebar-user-info">
                <strong>' . e($user?->name ?? '') . '</strong>
                <span>' . e($role) . '</span>
            </div>
        </div>
        <form method="POST" action="' . route('filament.admin.auth.logout') . '">
            <input type="hidden" name="_token" value="' . csrf_token() . '">
            <button type="submit" class="wado-sidebar-logout">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                    <polyline points="16 17 21 12 16 7"/>
                    <line x1="21" y1="12" x2="9" y2="12"/>
                </svg>
                Sign out
            </button>
        </form>
    </div>
</div>';
            })

            // ── Resources / Pages / Widgets ────────────────
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([Dashboard::class])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                AgentOverviewWidget::class,
                QuickActionsWidget::class,
                SuperAdminOverviewWidget::class,
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
