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

            'attention' => Tab::make('Needs attention')
                ->badge(
                    (clone $base)
                        ->where(function (Builder $query): void {
                            $query->whereIn('status', [
                                PaymentTransaction::STATUS_INITIATED,
                                PaymentTransaction::STATUS_PENDING,
                                PaymentTransaction::STATUS_FAILED,
                            ])->orWhere(function (Builder $innerQuery): void {
                                $innerQuery->where('status', PaymentTransaction::STATUS_CONFIRMED)
                                    ->whereNull('ticket_id');
                            });
                        })
                        ->count()
                )
                ->modifyQueryUsing(fn (Builder $query) => $query
                    ->where(function (Builder $innerQuery): void {
                        $innerQuery->whereIn('status', [
                            PaymentTransaction::STATUS_INITIATED,
                            PaymentTransaction::STATUS_PENDING,
                            PaymentTransaction::STATUS_FAILED,
                        ])->orWhere(function (Builder $nestedQuery): void {
                            $nestedQuery->where('status', PaymentTransaction::STATUS_CONFIRMED)
                                ->whereNull('ticket_id');
                        });
                    })),

            'refunded' => Tab::make('Refunds')
                ->badge((clone $base)->where('status', PaymentTransaction::STATUS_REFUNDED)->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', PaymentTransaction::STATUS_REFUNDED)),

            'delivery_gap' => Tab::make('No ticket')
                ->badge(
                    (clone $base)
                        ->where('status', PaymentTransaction::STATUS_CONFIRMED)
                        ->whereNull('ticket_id')
                        ->count()
                )
                ->modifyQueryUsing(fn (Builder $query) => $query
                    ->where('status', PaymentTransaction::STATUS_CONFIRMED)
                    ->whereNull('ticket_id')),
        ];
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
