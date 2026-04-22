<?php

namespace App\Filament\Widgets;

use App\Models\PaymentTransaction;
use App\Models\TicketScanLog;
use Filament\Widgets\Widget;

class AgentOverviewWidget extends Widget
{
    protected string $view = 'filament.widgets.agent-overview-widget';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 2;

    protected static ?string $pollingInterval = '5s';

    protected function getViewData(): array
    {
        $today = now()->startOfDay();

        $scansToday = TicketScanLog::query()
            ->where('staff_user_id', auth()->id())
            ->where('scanned_at', '>=', $today)
            ->count();

        $validScansToday = TicketScanLog::query()
            ->where('staff_user_id', auth()->id())
            ->where('scanned_at', '>=', $today)
            ->where('result', 'valid')
            ->count();

        $failedScansToday = TicketScanLog::query()
            ->where('staff_user_id', auth()->id())
            ->where('scanned_at', '>=', $today)
            ->where('result', '!=', 'valid')
            ->count();

        $pendingWalkinPayments = PaymentTransaction::query()
            ->whereIn('status', [
                PaymentTransaction::STATUS_PENDING,
                PaymentTransaction::STATUS_INITIATED,
            ])
            ->whereDate('created_at', now()->toDateString())
            ->count();

        return compact(
            'scansToday',
            'validScansToday',
            'failedScansToday',
            'pendingWalkinPayments',
        );
    }

    public static function canView(): bool
    {
        $user = auth()->user();

        return (bool) ($user?->isGateStaff() && ! $user?->canViewOperationsDashboard());
    }
}
