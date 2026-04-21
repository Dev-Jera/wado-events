<?php

namespace App\Filament\Pages;

use App\Models\Event;
use App\Models\PaymentTransaction;
use BackedEnum;
use Filament\Pages\Page;
use UnitEnum;

class FinancePage extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = 'Finance';

    protected static string|UnitEnum|null $navigationGroup = 'Operations';

    protected static ?int $navigationSort = 11;

    protected string $view = 'filament.pages.finance-page';

    public ?int $selectedEventId = null;

    public function mount(): void
    {
        $this->selectedEventId = (int) request()->integer('event_id') ?: null;
    }

    public function getEvents()
    {
        return Event::query()
            ->withSum(
                ['paymentTransactions as revenue_confirmed' => fn ($q) => $q->where('status', PaymentTransaction::STATUS_CONFIRMED)],
                'total_amount'
            )
            ->withSum(
                ['paymentTransactions as revenue_refunded' => fn ($q) => $q->where('status', PaymentTransaction::STATUS_REFUNDED)],
                'total_amount'
            )
            ->withSum(
                ['paymentTransactions as revenue_pending' => fn ($q) => $q->whereIn('status', [PaymentTransaction::STATUS_INITIATED, PaymentTransaction::STATUS_PENDING])],
                'total_amount'
            )
            ->withCount(['paymentTransactions as tickets_sold' => fn ($q) => $q->where('status', PaymentTransaction::STATUS_CONFIRMED)])
            ->withCount(['paymentTransactions as tickets_refunded' => fn ($q) => $q->where('status', PaymentTransaction::STATUS_REFUNDED)])
            ->orderByDesc('starts_at')
            ->get();
    }

    public function getSelectedEvent(): ?Event
    {
        if (! $this->selectedEventId) {
            return null;
        }

        return Event::find($this->selectedEventId);
    }

    public function getByChannel()
    {
        if (! $this->selectedEventId) return collect();

        return PaymentTransaction::query()
            ->where('event_id', $this->selectedEventId)
            ->where('status', PaymentTransaction::STATUS_CONFIRMED)
            ->selectRaw('payment_provider, SUM(total_amount) as total, SUM(quantity) as tickets')
            ->groupBy('payment_provider')
            ->orderByDesc('total')
            ->get();
    }

    public function getByCategory()
    {
        if (! $this->selectedEventId) return collect();

        return PaymentTransaction::query()
            ->where('event_id', $this->selectedEventId)
            ->where('status', PaymentTransaction::STATUS_CONFIRMED)
            ->selectRaw('ticket_category_id, SUM(total_amount) as total, SUM(quantity) as tickets')
            ->with('ticketCategory:id,name,price')
            ->groupBy('ticket_category_id')
            ->orderByDesc('total')
            ->get();
    }

    public function getSummary()
    {
        if (! $this->selectedEventId) return null;

        return PaymentTransaction::query()
            ->where('event_id', $this->selectedEventId)
            ->selectRaw("
                SUM(CASE WHEN status = ? THEN total_amount ELSE 0 END) as confirmed,
                SUM(CASE WHEN status = ? THEN total_amount ELSE 0 END) as refunded,
                SUM(CASE WHEN status IN (?, ?) THEN total_amount ELSE 0 END) as pending,
                SUM(CASE WHEN status = ? THEN quantity ELSE 0 END) as tickets_sold,
                SUM(CASE WHEN status = ? THEN quantity ELSE 0 END) as tickets_refunded
            ", [
                PaymentTransaction::STATUS_CONFIRMED,
                PaymentTransaction::STATUS_REFUNDED,
                PaymentTransaction::STATUS_INITIATED,
                PaymentTransaction::STATUS_PENDING,
                PaymentTransaction::STATUS_CONFIRMED,
                PaymentTransaction::STATUS_REFUNDED,
            ])
            ->first();
    }

    public static function canAccess(): bool
    {
        return (bool) auth()->user()?->isAdmin();
    }
}
