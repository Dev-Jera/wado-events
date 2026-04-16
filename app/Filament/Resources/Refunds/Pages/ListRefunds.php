<?php

namespace App\Filament\Resources\Refunds\Pages;

use App\Filament\Resources\Refunds\RefundResource;
use App\Models\PaymentTransaction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListRefunds extends ListRecords
{
    protected static string $resource = RefundResource::class;

    protected string $view = 'filament.resources.refunds.list-refunds';

    public function getTabs(): array
    {
        $base = $this->scopedRefundsQuery();

        return [
            'queue' => Tab::make('Refund Queue')
                ->badge(
                    (clone $base)
                        ->whereNotNull('refund_requested_at')
                        ->whereIn('status', [
                            PaymentTransaction::STATUS_CONFIRMED,
                            PaymentTransaction::STATUS_PENDING,
                            PaymentTransaction::STATUS_INITIATED,
                        ])
                        ->count()
                )
                ->modifyQueryUsing(fn (Builder $query) => $query
                    ->whereNotNull('refund_requested_at')
                    ->whereIn('status', [
                        PaymentTransaction::STATUS_CONFIRMED,
                        PaymentTransaction::STATUS_PENDING,
                        PaymentTransaction::STATUS_INITIATED,
                    ])),

            'refunded' => Tab::make('Refunded')
                ->badge((clone $base)->where('status', PaymentTransaction::STATUS_REFUNDED)->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', PaymentTransaction::STATUS_REFUNDED)),
        ];
    }

    public function getDefaultActiveTab(): string | int | null
    {
        return 'queue';
    }

    protected function scopedRefundsQuery(): Builder
    {
        $query = PaymentTransaction::query();
        $user = auth()->user();

        if ($user?->isEventOwner()) {
            $query->whereHas('event', fn (Builder $eventQuery) => $eventQuery->where('user_id', $user->id));
        }

        return $query;
    }
}
