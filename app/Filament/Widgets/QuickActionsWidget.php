<?php

namespace App\Filament\Widgets;

use App\Filament\Pages\GatePortalPage;
use App\Filament\Pages\ScannerPage;
use App\Filament\Resources\Events\EventResource;
use App\Filament\Resources\PaymentTransactions\PaymentTransactionResource;
use Filament\Widgets\Widget;

class QuickActionsWidget extends Widget
{
    protected string $view = 'filament.widgets.quick-actions-widget';

    protected static ?int $sort = 20;

    protected int|string|array $columnSpan = 'full';

    protected function getViewData(): array
    {
        $user = auth()->user();

        $isGateOnly = (bool) ($user?->isGateStaff() && ! $user?->canViewOperationsDashboard());

        $consoleTitle = match (true) {
            (bool) $user?->isEventOwner() => 'Owner Console',
            $isGateOnly => 'Gate Console',
            (bool) $user?->isVerificationOfficer() => 'Verification Console',
            default => 'Operations Console',
        };

        $consoleHint = match (true) {
            (bool) $user?->isEventOwner() => 'Manage your events, payments, and refunds in one place.',
            $isGateOnly => 'Run walk-in sales, scanner checks, and gate flow from this workspace.',
            (bool) $user?->isVerificationOfficer() => 'Monitor scan integrity and verification activity.',
            default => 'Use quick shortcuts to key operational tools.',
        };

        return [
            'createEventUrl'  => EventResource::getUrl('create'),
            'scannerUrl'      => ScannerPage::getUrl(),
            'paymentsUrl'     => PaymentTransactionResource::getUrl(),
            'gatePortalUrl'   => GatePortalPage::getUrl(),
            'canUseGateTools' => (bool) $user?->isGateStaff(),
            'canCreateEvent'  => (bool) ($user?->isAdmin() || $user?->isSuperAdmin() || $user?->isEventOwner()),
            'canViewPayments' => (bool) $user?->canViewOperationsDashboard(),
            'consoleTitle'    => $consoleTitle,
            'consoleHint'     => $consoleHint,
        ];
    }

    public static function canView(): bool
    {
        $user = auth()->user();
        return (bool) ($user?->canAccessOperationsPanel());
    }
}
