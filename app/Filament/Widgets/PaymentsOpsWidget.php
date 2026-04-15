<?php

namespace App\Filament\Widgets;

use App\Models\PaymentTransaction;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Builder;

class PaymentsOpsWidget extends Widget
{
    protected string $view = 'filament.widgets.payments-ops-widget';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 1;

    protected function getViewData(): array
    {
        $base = $this->scopedPaymentsQuery();

        $latestUpdate = (clone $base)->latest('updated_at')->value('updated_at');

        $pendingPayments = (clone $base)->where('status', PaymentTransaction::STATUS_PENDING)->count();
        $failedPayments = (clone $base)->where('status', PaymentTransaction::STATUS_FAILED)->count();
        $confirmedNoTicket = (clone $base)->where('status', PaymentTransaction::STATUS_CONFIRMED)
            ->whereNull('ticket_id')
            ->count();
        $ticketsPendingIssue = (int) (clone $base)->where('status', PaymentTransaction::STATUS_CONFIRMED)
            ->whereNull('ticket_id')
            ->sum('quantity');
        $confirmedToday = (clone $base)->where('status', PaymentTransaction::STATUS_CONFIRMED)
            ->whereDate('created_at', today())
            ->count();
        $totalCollected = (float) (clone $base)->where('status', PaymentTransaction::STATUS_CONFIRMED)->sum('total_amount');
        $eventsWithOpenPayments = (clone $base)->whereIn('status', [
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

        return (bool) ($user?->canViewOperationsDashboard());
    }

    protected function scopedPaymentsQuery(): Builder
    {
        $query = PaymentTransaction::query();
        $user = auth()->user();

        if ($user?->isEventOwner()) {
            $query->whereHas('event', fn (Builder $eventQuery) => $eventQuery->where('user_id', $user->id));
        }

        return $query;
    }
}
