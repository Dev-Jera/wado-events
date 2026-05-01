<?php

namespace App\Filament\Widgets;

use App\Models\Event;
use App\Models\PaymentTransaction;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;

class PaymentsOpsWidget extends Widget
{
    protected string $view = 'filament.widgets.payments-ops-widget';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 1;

    #[Url(as: 'event_id')]
    public int $selectedEventId = 0;

    public function updatedSelectedEventId(): void
    {
        $this->dispatch('event-filter-changed', eventId: $this->selectedEventId);
    }

    protected function getViewData(): array
    {
        $base = $this->scopedPaymentsQuery();
        $user = auth()->user();

        $latestUpdate = (clone $base)->latest('updated_at')->value('updated_at');

        $pendingPayments     = (clone $base)->where('status', PaymentTransaction::STATUS_PENDING)->count();
        $failedPayments      = (clone $base)->where('status', PaymentTransaction::STATUS_FAILED)->count();
        $confirmedNoTicket   = (clone $base)->where('status', PaymentTransaction::STATUS_CONFIRMED)->whereNull('ticket_id')->count();
        $ticketsPendingIssue = (int) (clone $base)->where('status', PaymentTransaction::STATUS_CONFIRMED)->whereNull('ticket_id')->sum('quantity');
        $confirmedToday      = (clone $base)->where('status', PaymentTransaction::STATUS_CONFIRMED)->whereDate('created_at', today())->count();
        $eventsWithOpenPayments = (clone $base)->whereIn('status', [
            PaymentTransaction::STATUS_PENDING,
            PaymentTransaction::STATUS_FAILED,
        ])->distinct('event_id')->count('event_id');

        $events = Event::query()
            ->when($user?->isEventOwner(), fn (Builder $q) => $q->where('user_id', $user->id))
            ->orderBy('title')
            ->get(['id', 'title']);

        $selectedEvent = $this->selectedEventId > 0
            ? $events->firstWhere('id', $this->selectedEventId)
            : null;

        $lastSync = $latestUpdate ? now()->diffForHumans($latestUpdate, true) . ' ago' : 'just now';

        $selectedEventId = $this->selectedEventId;

        return compact(
            'pendingPayments', 'failedPayments', 'confirmedNoTicket',
            'ticketsPendingIssue', 'confirmedToday', 'lastSync',
            'eventsWithOpenPayments', 'events', 'selectedEventId', 'selectedEvent',
        );
    }

    public static function canView(): bool
    {
        return (bool) (auth()->user()?->canViewOperationsDashboard());
    }

    protected function scopedPaymentsQuery(): Builder
    {
        $query = PaymentTransaction::query();
        $user  = auth()->user();

        if ($user?->isEventOwner()) {
            $query->whereHas('event', fn (Builder $q) => $q->where('user_id', $user->id));
        }

        if ($this->selectedEventId > 0) {
            $query->where('event_id', $this->selectedEventId);
        }

        return $query;
    }
}
