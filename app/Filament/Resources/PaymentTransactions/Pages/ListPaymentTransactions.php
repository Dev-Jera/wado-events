<?php

namespace App\Filament\Resources\PaymentTransactions\Pages;

use App\Filament\Resources\PaymentTransactions\PaymentTransactionResource;
use App\Filament\Widgets\PaymentsOpsWidget;
use App\Models\PaymentTransaction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListPaymentTransactions extends ListRecords
{
    protected static string $resource = PaymentTransactionResource::class;

    protected string $view = 'filament.resources.payment-transactions.list-payment-transactions';

    protected function getHeaderWidgets(): array
    {
        return [
            PaymentsOpsWidget::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int|array
    {
        return 1;
    }

    public function getTabs(): array
    {
        $base = $this->scopedPaymentsQuery();

        return [
            'all' => Tab::make('All')
                ->badge((clone $base)->count()),

            'confirmed' => Tab::make('Confirmed')
                ->badge((clone $base)->where('status', PaymentTransaction::STATUS_CONFIRMED)->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', PaymentTransaction::STATUS_CONFIRMED)),

            'pending' => Tab::make('Pending')
                ->badge((clone $base)->where('status', PaymentTransaction::STATUS_PENDING)->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', PaymentTransaction::STATUS_PENDING)),

            'failed' => Tab::make('Failed')
                ->badge((clone $base)->where('status', PaymentTransaction::STATUS_FAILED)->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', PaymentTransaction::STATUS_FAILED)),

            'refund_queue' => Tab::make('Refund queue')
                ->badge(
                    (clone $base)
                        ->whereIn('status', [
                            PaymentTransaction::STATUS_CONFIRMED,
                            PaymentTransaction::STATUS_PENDING,
                            PaymentTransaction::STATUS_INITIATED,
                        ])
                        ->where(function (Builder $query): void {
                            $query->whereNull('ticket_id')
                                ->orWhereHas('ticket', fn (Builder $ticketQuery) => $ticketQuery->whereNull('used_at'));
                        })
                        ->count()
                )
                ->modifyQueryUsing(fn (Builder $query) => $query
                    ->whereIn('status', [
                        PaymentTransaction::STATUS_CONFIRMED,
                        PaymentTransaction::STATUS_PENDING,
                        PaymentTransaction::STATUS_INITIATED,
                    ])
                    ->where(function (Builder $innerQuery): void {
                        $innerQuery->whereNull('ticket_id')
                            ->orWhereHas('ticket', fn (Builder $ticketQuery) => $ticketQuery->whereNull('used_at'));
                    })),

            'refunded' => Tab::make('Refunded')
                ->badge((clone $base)->where('status', PaymentTransaction::STATUS_REFUNDED)->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', PaymentTransaction::STATUS_REFUNDED)),

            'no_ticket' => Tab::make('No ticket')
                ->badge((clone $base)->where('status', PaymentTransaction::STATUS_CONFIRMED)->whereNull('ticket_id')->count())
                ->modifyQueryUsing(fn (Builder $query) => $query
                    ->where('status', PaymentTransaction::STATUS_CONFIRMED)
                    ->whereNull('ticket_id')),
        ];
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
