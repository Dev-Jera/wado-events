<?php

namespace App\Http\Controllers;

use App\Jobs\IssueTicketForPayment;
use App\Models\PaymentTransaction;
use App\Services\Payment\PaymentLifecycleService;
use App\Services\Payment\PaymentNotificationService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function adminIndex(Request $request)
    {
        $this->authorize('manageAdminPayments', PaymentTransaction::class);

        $status = strtoupper(trim((string) $request->query('status', '')));
        $search = trim((string) $request->query('q', ''));

        $query = PaymentTransaction::query()
            ->with(['user', 'event', 'ticket'])
            ->latest('id');

        if ($status !== '') {
            $query->where('status', $status);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search): void {
                $q->where('idempotency_key', 'like', '%' . $search . '%')
                    ->orWhere('provider_reference', 'like', '%' . $search . '%')
                    ->orWhere('phone_number', 'like', '%' . $search . '%')
                    ->orWhereHas('user', function ($userQuery) use ($search): void {
                        $userQuery->where('email', 'like', '%' . $search . '%')
                            ->orWhere('name', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('event', function ($eventQuery) use ($search): void {
                        $eventQuery->where('title', 'like', '%' . $search . '%');
                    });
            });
        }

        $payments = $query->paginate(20)->withQueryString();

        $statusCounts = PaymentTransaction::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('pages.payments.admin-index', [
            'payments' => $payments,
            'statusFilter' => $status,
            'searchTerm' => $search,
            'statusCounts' => $statusCounts,
        ]);
    }

    public function adminResend(Request $request, PaymentTransaction $paymentTransaction, PaymentNotificationService $notificationService)
    {
        $this->authorize('manageAdminPayments', PaymentTransaction::class);

        if ($paymentTransaction->status !== PaymentTransaction::STATUS_CONFIRMED) {
            return back()->with('error', 'Only CONFIRMED payments can be resent.');
        }

        if (! $paymentTransaction->ticket_id) {
            IssueTicketForPayment::dispatch($paymentTransaction->id);

            return back()->with('success', 'Ticket issuance retry queued.');
        }

        $paymentTransaction->loadMissing('ticket.user');
        $notificationService->sendTicketConfirmed($paymentTransaction->ticket, $paymentTransaction);

        return back()->with('success', 'Ticket email/SMS resend attempted.');
    }

    public function adminConfirm(Request $request, PaymentTransaction $paymentTransaction)
    {
        $this->authorize('manageAdminPayments', PaymentTransaction::class);

        if (! in_array($paymentTransaction->status, [
            PaymentTransaction::STATUS_PENDING,
            PaymentTransaction::STATUS_INITIATED,
        ], true)) {
            return back()->with('error', 'Only PENDING or INITIATED payments can be manually confirmed.');
        }

        $paymentTransaction->forceFill([
            'status'       => PaymentTransaction::STATUS_CONFIRMED,
            'confirmed_at' => now(),
            'last_error'   => null,
        ])->save();

        IssueTicketForPayment::dispatch($paymentTransaction->id);

        return back()->with('success', "Payment #{$paymentTransaction->id} confirmed. Ticket is being issued.");
    }

    public function adminRefund(Request $request, PaymentTransaction $paymentTransaction, PaymentLifecycleService $paymentLifecycleService)
    {
        $this->authorize('manageAdminPayments', PaymentTransaction::class);

        if (! in_array($paymentTransaction->status, [
            PaymentTransaction::STATUS_CONFIRMED,
            PaymentTransaction::STATUS_PENDING,
            PaymentTransaction::STATUS_INITIATED,
        ], true)) {
            return back()->with('error', 'Only CONFIRMED, PENDING, or INITIATED payments can be refunded.');
        }

        $paymentTransaction->loadMissing('ticket');

        if ($paymentTransaction->ticket?->used_at) {
            return back()->with('error', 'Scanned tickets cannot be refunded from admin.');
        }

        $data = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ]);

        $actor = $request->user()?->name ?: 'admin';
        $reason = trim((string) $data['reason']);

        $result = $paymentLifecycleService->refundWithProvider(
            $paymentTransaction->fresh(['ticket']),
            "Manual refund by {$actor}. Reason: {$reason}"
        );

        if (! ($result['ok'] ?? false)) {
            return back()->with('error', (string) ($result['message'] ?? 'MarzPay refund failed.'));
        }

        return back()->with('success', "Payment #{$paymentTransaction->id} refund submitted. " . (string) ($result['message'] ?? ''));
    }

}
