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
