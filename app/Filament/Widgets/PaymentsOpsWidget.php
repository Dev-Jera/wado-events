<?php

namespace App\Filament\Widgets;

use App\Models\Event;
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
        $selectedEventId = request()->integer('event_id');
        $user = auth()->user();

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
        $eventsWithOpenPayments = (clone $base)->whereIn('status', [
            PaymentTransaction::STATUS_PENDING,
            PaymentTransaction::STATUS_FAILED,
        ])->distinct('event_id')->count('event_id');

        $events = Event::query()
            ->when($user?->isEventOwner(), fn (Builder $query) => $query->where('user_id', $user->id))
            ->orderBy('title')
            ->get(['id', 'title']);

        $selectedEvent = $selectedEventId > 0
            ? $events->firstWhere('id', $selectedEventId)
            : null;

        $lastSync = $latestUpdate ? now()->diffForHumans($latestUpdate, true) . ' ago' : 'just now';

        return compact(
            'pendingPayments',
            'failedPayments',
            'confirmedNoTicket',
            'ticketsPendingIssue',
            'confirmedToday',
            'lastSync',
            'eventsWithOpenPayments',
            'events',
            'selectedEventId',
            'selectedEvent',
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
        $selectedEventId = request()->integer('event_id');

        if ($user?->isEventOwner()) {
            $query->whereHas('event', fn (Builder $eventQuery) => $eventQuery->where('user_id', $user->id));
        }

        if ($selectedEventId > 0) {
            $query->where('event_id', $selectedEventId);
        }

        return $query;
    }
}
