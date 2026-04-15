<?php

namespace App\Filament\Widgets;

use App\Models\Event;
use App\Models\PaymentTransaction;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\User;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class SuperAdminOverviewWidget extends Widget
{
    protected static ?int $sort = 1;

    protected string $view = 'filament.widgets.super-admin-overview-widget';

    protected int|string|array $columnSpan = 'full';

    protected function getViewData(): array
    {
        $now       = Carbon::now();
        $thisMonth = $now->month;
        $thisYear  = $now->year;
        $lastMonth = $now->copy()->subMonth();
        $eventsBase = $this->scopedEventsQuery();
        $paymentsBase = $this->scopedPaymentsQuery();
        $ticketsBase = $this->scopedTicketsQuery();
        $ticketCategoriesBase = $this->scopedTicketCategoriesQuery();

        // ── Needs Attention ──────────────────────────────────────────────────
        $pendingPayments    = (clone $paymentsBase)->where('status', PaymentTransaction::STATUS_PENDING)->count();
        $failedPayments     = (clone $paymentsBase)->where('status', PaymentTransaction::STATUS_FAILED)->count();
        $qrNotIssued        = (int) (clone $ticketsBase)->whereNull('qr_code_path')->sum('quantity');

        $issuedQr           = (int) (clone $ticketsBase)->whereNotNull('qr_code_path')->sum('quantity');
        $scanned            = (int) (clone $ticketsBase)->where(function ($q): void {
            $q->where('status', 'used')->orWhereNotNull('used_at');
        })->sum('quantity');
        $atGateUnscanned    = max($issuedQr - $scanned, 0);
        $inventoryRemaining = (int) (clone $ticketCategoriesBase)->sum('tickets_remaining');

        // ── Summary Stats ────────────────────────────────────────────────────
        $totalEvents        = (clone $eventsBase)->count();
        $newEventsThisMonth = (clone $eventsBase)->whereMonth('created_at', $thisMonth)
            ->whereYear('created_at', $thisYear)->count();

        $ticketsSoldThisMonth = (int) (clone $paymentsBase)->where('status', PaymentTransaction::STATUS_CONFIRMED)
            ->whereMonth('created_at', $thisMonth)->whereYear('created_at', $thisYear)->sum('quantity');
        $ticketsSoldLastMonth = (int) (clone $paymentsBase)->where('status', PaymentTransaction::STATUS_CONFIRMED)
            ->whereMonth('created_at', $lastMonth->month)->whereYear('created_at', $lastMonth->year)->sum('quantity');
        $ticketsSoldTotal     = (int) (clone $paymentsBase)->where('status', PaymentTransaction::STATUS_CONFIRMED)->sum('quantity');
        $ticketsPctChange     = $ticketsSoldLastMonth > 0
            ? round((($ticketsSoldThisMonth - $ticketsSoldLastMonth) / $ticketsSoldLastMonth) * 100)
            : 0;

        $revenueThisMonth = (float) (clone $paymentsBase)->where('status', PaymentTransaction::STATUS_CONFIRMED)
            ->whereMonth('created_at', $thisMonth)->whereYear('created_at', $thisYear)->sum('total_amount');
        $revenueLastMonth = (float) (clone $paymentsBase)->where('status', PaymentTransaction::STATUS_CONFIRMED)
            ->whereMonth('created_at', $lastMonth->month)->whereYear('created_at', $lastMonth->year)->sum('total_amount');
        $revenueTotal     = (float) (clone $paymentsBase)->where('status', PaymentTransaction::STATUS_CONFIRMED)->sum('total_amount');
        $revenuePctChange = $revenueLastMonth > 0
            ? round((($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100)
            : 0;

        $totalGateAgents = User::whereIn('role', ['agent', 'gate', 'gate_agent'])->count();
        $inactiveAgents  = 0;

        // ── Events overview ──────────────────────────────────────────────────
        $events = (clone $eventsBase)
            ->with('ticketCategories')
            ->latest()
            ->take(8)
            ->get()
            ->map(function (Event $event): array {
            $capacity = $event->ticketCategories->sum('ticket_count');
            $sold     = $capacity - $event->ticketCategories->sum('tickets_remaining');
            $revenue  = (float) PaymentTransaction::where('event_id', $event->id)
                ->where('status', PaymentTransaction::STATUS_CONFIRMED)
                ->sum('total_amount');

            return [
                'title'    => $event->title,
                'capacity' => (int) $capacity,
                'sold'     => (int) $sold,
                'revenue'  => $revenue,
                'status'   => $event->live_status,
            ];
        });

        // ── Chart data (last 6 months) ────────────────────────────────────────
        $chartData   = [];
        $maxBarValue = 1;

        for ($i = 5; $i >= 0; $i--) {
            $month        = $now->copy()->subMonths($i);
            $issuedMonth  = (int) (clone $ticketsBase)->whereNotNull('qr_code_path')
                ->whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->sum('quantity');
            $scannedMonth = (int) (clone $ticketsBase)->where(function ($q): void {
                $q->where('status', 'used')->orWhereNotNull('used_at');
            })->whereMonth('updated_at', $month->month)
                ->whereYear('updated_at', $month->year)
                ->sum('quantity');

            $chartData[] = [
                'month'   => $month->format('M'),
                'issued'  => $issuedMonth,
                'scanned' => $scannedMonth,
            ];
            $maxBarValue = max($maxBarValue, $issuedMonth, $scannedMonth);
        }

        // ── Recent payments ──────────────────────────────────────────────────
        $recentPayments = (clone $paymentsBase)
            ->with('event')
            ->latest()
            ->take(5)
            ->get()
            ->map(function (PaymentTransaction $payment): array {
                return [
                    'holder'   => $payment->holder_name ?: 'Unknown',
                    'event'    => Str::limit($payment->event?->title ?? 'Unknown Event', 22),
                    'phone'    => $payment->phone_number ?: '—',
                    'amount'   => (float) $payment->total_amount,
                    'currency' => $payment->currency ?? 'UGX',
                    'provider' => $payment->payment_provider ?? 'MarzePay',
                    'status'   => strtolower($payment->status),
                ];
            });

        return compact(
            'pendingPayments', 'failedPayments', 'qrNotIssued', 'atGateUnscanned', 'inventoryRemaining',
            'totalEvents', 'newEventsThisMonth',
            'ticketsSoldThisMonth', 'ticketsSoldLastMonth', 'ticketsSoldTotal', 'ticketsPctChange',
            'revenueThisMonth', 'revenueLastMonth', 'revenueTotal', 'revenuePctChange',
            'totalGateAgents', 'inactiveAgents',
            'events', 'chartData', 'maxBarValue', 'recentPayments'
        );
    }

    public static function canView(): bool
    {
        $user = auth()->user();

        return (bool) ($user?->canViewOperationsDashboard());
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

    protected function scopedTicketsQuery(): Builder
    {
        $query = Ticket::query();
        $user = auth()->user();

        if ($user?->isEventOwner()) {
            $query->whereHas('event', fn (Builder $eventQuery) => $eventQuery->where('user_id', $user->id));
        }

        return $query;
    }

    protected function scopedTicketCategoriesQuery(): Builder
    {
        $query = TicketCategory::query();
        $user = auth()->user();

        if ($user?->isEventOwner()) {
            $query->whereHas('event', fn (Builder $eventQuery) => $eventQuery->where('user_id', $user->id));
        }

        return $query;
    }
}
