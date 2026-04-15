<?php

namespace App\Filament\Widgets;

use App\Models\PaymentTransaction;
use Filament\Widgets\Widget;

class PaymentsOpsWidget extends Widget
{
    protected string $view = 'filament.widgets.payments-ops-widget';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 1;

    protected function getViewData(): array
    {
        $latestUpdate = PaymentTransaction::latest('updated_at')->value('updated_at');

        $pendingPayments = PaymentTransaction::where('status', PaymentTransaction::STATUS_PENDING)->count();
        $failedPayments = PaymentTransaction::where('status', PaymentTransaction::STATUS_FAILED)->count();
        $confirmedNoTicket = PaymentTransaction::where('status', PaymentTransaction::STATUS_CONFIRMED)
            ->whereNull('ticket_id')
            ->count();
        $ticketsPendingIssue = (int) PaymentTransaction::where('status', PaymentTransaction::STATUS_CONFIRMED)
            ->whereNull('ticket_id')
            ->sum('quantity');
        $confirmedToday = PaymentTransaction::where('status', PaymentTransaction::STATUS_CONFIRMED)
            ->whereDate('created_at', today())
            ->count();
        $totalCollected = (float) PaymentTransaction::where('status', PaymentTransaction::STATUS_CONFIRMED)->sum('total_amount');
        $eventsWithOpenPayments = PaymentTransaction::whereIn('status', [
            PaymentTransaction::STATUS_PENDING,
            PaymentTransaction::STATUS_FAILED,
        ])->distinct('event_id')->count('event_id');

        $lastSync = $latestUpdate ? now()->diffForHumans($latestUpdate, true) . ' ago' : 'just now';

        return compact(
            'pendingPayments',
            'failedPayments',
            'confirmedNoTicket',
            'ticketsPendingIssue',
            'confirmedToday',
            'totalCollected',
            'lastSync',
            'eventsWithOpenPayments',
        );
    }

    public static function canView(): bool
    {
        $user = auth()->user();

        return (bool) ($user?->isAdmin() || $user?->isSuperAdmin());
    }
}
