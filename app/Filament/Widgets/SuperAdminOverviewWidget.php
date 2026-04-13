<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\PaymentTransactions\PaymentTransactionResource;
use App\Models\Event;
use App\Models\PaymentTransaction;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SuperAdminOverviewWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $events = Event::query()->count();
        $capacity = (int) TicketCategory::query()->sum('ticket_count');
        $inventoryRemaining = (int) TicketCategory::query()->sum('tickets_remaining');
        $issuedQr = (int) Ticket::query()->whereNotNull('qr_code_path')->sum('quantity');
        $scanned = (int) Ticket::query()->where(function ($q): void {
            $q->where('status', 'used')->orWhereNotNull('used_at');
        })->sum('quantity');
        $pendingPayments = PaymentTransaction::query()->where('status', PaymentTransaction::STATUS_PENDING)->count();
        $failedPayments = PaymentTransaction::query()->where('status', PaymentTransaction::STATUS_FAILED)->count();
        $gateAgents = User::query()->whereIn('role', ['agent', 'gate', 'gate_agent'])->count();

        return [
            Stat::make('Total Events', (string) $events)
                ->description('Events currently in system')
                ->color('primary'),

            Stat::make('Allocated Tickets', number_format($capacity))
                ->description('From all event ticket categories')
                ->color('info'),

            Stat::make('Inventory Remaining', number_format($inventoryRemaining))
                ->description('Unsold seats/tickets left')
                ->color('warning'),

            Stat::make('QR Issued', number_format($issuedQr))
                ->description('Tickets issued with QR code')
                ->color('success'),

            Stat::make('Scanned', number_format($scanned))
                ->description('Used/validated at gate')
                ->color('success'),

            Stat::make('At Gate Remaining', number_format(max($issuedQr - $scanned, 0)))
                ->description('Issued but not yet scanned')
                ->color('warning'),

            Stat::make('Pending Payments', (string) $pendingPayments)
                ->description('Awaiting completion')
                ->url(PaymentTransactionResource::getUrl())
                ->color('warning'),

            Stat::make('Failed Payments', (string) $failedPayments)
                ->description('Need support follow-up')
                ->url(PaymentTransactionResource::getUrl())
                ->color('danger'),

            Stat::make('Gate Agents', (string) $gateAgents)
                ->description('Active gate staff accounts')
                ->url(route('admin.users.index'))
                ->color('primary'),
        ];
    }

    public static function canView(): bool
    {
        $user = auth()->user();
        return $user?->isAdmin() || $user?->isSuperAdmin() ?? false;
    }
}
