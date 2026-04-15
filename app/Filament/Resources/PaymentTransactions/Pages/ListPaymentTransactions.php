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
        return [
            'all' => Tab::make('All')
                ->badge(PaymentTransaction::query()->count()),

            'confirmed' => Tab::make('Confirmed')
                ->badge(PaymentTransaction::where('status', PaymentTransaction::STATUS_CONFIRMED)->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', PaymentTransaction::STATUS_CONFIRMED)),

            'pending' => Tab::make('Pending')
                ->badge(PaymentTransaction::where('status', PaymentTransaction::STATUS_PENDING)->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', PaymentTransaction::STATUS_PENDING)),

            'failed' => Tab::make('Failed')
                ->badge(PaymentTransaction::where('status', PaymentTransaction::STATUS_FAILED)->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', PaymentTransaction::STATUS_FAILED)),

            'no_ticket' => Tab::make('No ticket')
                ->badge(PaymentTransaction::where('status', PaymentTransaction::STATUS_CONFIRMED)->whereNull('ticket_id')->count())
                ->modifyQueryUsing(fn (Builder $query) => $query
                    ->where('status', PaymentTransaction::STATUS_CONFIRMED)
                    ->whereNull('ticket_id')),
        ];
    }
}
