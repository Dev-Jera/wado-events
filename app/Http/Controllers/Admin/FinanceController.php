<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\PaymentTransaction;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FinanceController extends Controller
{
    public function index()
    {
        $events = Event::query()
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
            ->withCount(
                ['paymentTransactions as tickets_sold' => fn ($q) => $q->where('status', PaymentTransaction::STATUS_CONFIRMED)]
            )
            ->withCount(
                ['paymentTransactions as tickets_refunded' => fn ($q) => $q->where('status', PaymentTransaction::STATUS_REFUNDED)]
            )
            ->orderByDesc('starts_at')
            ->get();

        return view('pages.admin.finance.index', compact('events'));
    }

    public function show(Event $event, Request $request)
    {
        if ($request->boolean('export')) {
            return $this->exportCsv($event);
        }

        $event->load('ticketCategories');

        $byChannel = PaymentTransaction::query()
            ->where('event_id', $event->id)
            ->where('status', PaymentTransaction::STATUS_CONFIRMED)
            ->selectRaw('payment_provider, SUM(total_amount) as total, SUM(quantity) as tickets')
            ->groupBy('payment_provider')
            ->orderByDesc('total')
            ->get();

        $byCategory = PaymentTransaction::query()
            ->where('event_id', $event->id)
            ->where('status', PaymentTransaction::STATUS_CONFIRMED)
            ->selectRaw('ticket_category_id, SUM(total_amount) as total, SUM(quantity) as tickets')
            ->with('ticketCategory:id,name,price')
            ->groupBy('ticket_category_id')
            ->orderByDesc('total')
            ->get();

        $summary = PaymentTransaction::query()
            ->where('event_id', $event->id)
            ->selectRaw("
                SUM(CASE WHEN status = ? THEN total_amount ELSE 0 END) as confirmed,
                SUM(CASE WHEN status = ? THEN total_amount ELSE 0 END) as refunded,
                SUM(CASE WHEN status IN (?, ?) THEN total_amount ELSE 0 END) as pending,
                SUM(CASE WHEN status = ? THEN total_amount ELSE 0 END) as failed,
                SUM(CASE WHEN status = ? THEN quantity ELSE 0 END) as tickets_sold,
                SUM(CASE WHEN status = ? THEN quantity ELSE 0 END) as tickets_refunded
            ", [
                PaymentTransaction::STATUS_CONFIRMED,
                PaymentTransaction::STATUS_REFUNDED,
                PaymentTransaction::STATUS_INITIATED,
                PaymentTransaction::STATUS_PENDING,
                PaymentTransaction::STATUS_CONFIRMED,
                PaymentTransaction::STATUS_REFUNDED,
                PaymentTransaction::STATUS_REFUNDED,
            ])
            ->first();

        return view('pages.admin.finance.show', compact('event', 'byChannel', 'byCategory', 'summary'));
    }

    protected function exportCsv(Event $event): StreamedResponse
    {
        $filename = 'finance-' . str($event->title)->slug() . '-' . now()->format('Ymd') . '.csv';

        return response()->streamDownload(function () use ($event) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Date', 'Holder', 'Channel', 'Category', 'Qty', 'Amount (UGX)', 'Status', 'Reference']);

            PaymentTransaction::query()
                ->where('event_id', $event->id)
                ->with(['ticketCategory:id,name'])
                ->orderByDesc('created_at')
                ->chunk(200, function ($rows) use ($handle) {
                    foreach ($rows as $row) {
                        fputcsv($handle, [
                            $row->created_at?->format('Y-m-d H:i'),
                            $row->holder_name,
                            strtoupper((string) $row->payment_provider),
                            $row->ticketCategory?->name ?? '—',
                            $row->quantity,
                            number_format((float) $row->total_amount, 2, '.', ''),
                            strtoupper((string) $row->status),
                            $row->idempotency_key,
                        ]);
                    }
                });

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
