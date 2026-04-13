<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Events\EventResource;
use App\Filament\Resources\PaymentTransactions\PaymentTransactionResource;
use Filament\Widgets\Widget;

class QuickActionsWidget extends Widget
{
    protected string $view = 'filament.widgets.quick-actions-widget';

    protected static ?int $sort = -1;

    protected int|string|array $columnSpan = 'full';

    protected function getViewData(): array
    {
        return [
            'createEventUrl'  => EventResource::getUrl('create'),
            'scannerUrl'      => route('tickets.verify.index'),
            'paymentsUrl'     => PaymentTransactionResource::getUrl(),
            'gatePortalUrl'   => route('gate.portal'),
        ];
    }

    public static function canView(): bool
    {
        $user = auth()->user();
        return $user?->isAdmin() || $user?->isSuperAdmin() ?? false;
    }
}
