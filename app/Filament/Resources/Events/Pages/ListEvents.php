<?php

namespace App\Filament\Resources\Events\Pages;

use App\Filament\Resources\Events\EventResource;
use App\Models\Event;
use App\Models\PaymentTransaction;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListEvents extends ListRecords
{
    protected static string $resource = EventResource::class;

    protected string $view = 'filament.resources.events.list-events';

    // ── Selected event state ─────────────────────────────────────────
    public ?int $selectedEventId = null;

    public function mount(): void
    {
        parent::mount();
        // Auto-select the first event on page load
        $this->selectedEventId = $this->scopedEventsQuery()->latest()->first()?->id;
    }

    /** Called when a table row is clicked */
    public function selectEvent(int $eventId): void
    {
        $this->selectedEventId = $eventId;
    }

    /** Called when the X button is clicked */
    public function closePanel(): void
    {
        $this->selectedEventId = null;
    }

    // ── Detail panel data (recomputed whenever selectedEventId changes) ─
    public function getSelectedEvent(): ?Event
    {
        if (! $this->selectedEventId) {
            return null;
        }

        return $this->scopedEventsQuery()
            ->with(['ticketCategories', 'category'])
            ->find($this->selectedEventId);
    }

    public function getSelectedEventStats(): array
    {
        if (! $this->selectedEventId) {
            return [];
        }

        $event = $this->getSelectedEvent();

        if (! $event) {
            return [];
        }

        $capacity    = (int) $event->ticketCategories->sum('ticket_count');
        $ticketsSold = (int) PaymentTransaction::where('event_id', $event->id)
            ->where('status', 'CONFIRMED')
            ->sum('quantity');
        $revenue     = (float) PaymentTransaction::where('event_id', $event->id)
            ->where('status', 'CONFIRMED')
            ->sum('total_amount');

        $recentAttendees = PaymentTransaction::where('event_id', $event->id)
            ->where('status', 'CONFIRMED')
            ->with(['user', 'ticketCategory'])
            ->latest('confirmed_at')
            ->take(6)
            ->get();

        $allTickets = PaymentTransaction::where('event_id', $event->id)
            ->with(['user', 'ticketCategory'])
            ->latest()
            ->take(20)
            ->get();

        return compact('event', 'capacity', 'ticketsSold', 'revenue', 'recentAttendees', 'allTickets');
    }

    // ── Page meta ────────────────────────────────────────────────────
    public function getTitle(): string
    {
        return 'Manage events';
    }

    public function getSubheading(): ?string
    {
        $events = $this->scopedEventsQuery();
        $payments = $this->scopedPaymentsQuery();

        $total   = (clone $events)->count();
        $revenue = (float) (clone $payments)->where('status', 'CONFIRMED')->sum('total_amount');

        return "{$total} events · UGX " . number_format($revenue, 0) . ' total revenue';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('+ Create Event')
                ->visible(fn (): bool => EventResource::canCreate()),
        ];
    }

    public function getTabs(): array
    {
        $events = $this->scopedEventsQuery();

        return [
            'all' => Tab::make('All')
                ->badge((clone $events)->count()),

            'published' => Tab::make('Published')
                ->badge((clone $events)->where('status', 'published')->count())
                ->modifyQueryUsing(fn (Builder $q) => $q->where('status', 'published')),

            'draft' => Tab::make('Draft')
                ->badge((clone $events)->where('status', 'draft')->count())
                ->modifyQueryUsing(fn (Builder $q) => $q->where('status', 'draft')),

            'cancelled' => Tab::make('Cancelled')
                ->badge((clone $events)->where('status', 'cancelled')->count())
                ->modifyQueryUsing(fn (Builder $q) => $q->where('status', 'cancelled')),
        ];
    }

    protected function scopedEventsQuery(): Builder
    {
        $query = Event::query();
        $user = auth()->user();

        if ($user?->isEventOwner()) {
            $query->where('user_id', $user->id);
        }

        return $query;
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
