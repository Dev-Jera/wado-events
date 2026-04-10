<?php

namespace App\Http\Controllers;

use App\Jobs\IssueTicketForPayment;
use App\Models\PaymentTransaction;
use App\Services\Payment\PaymentNotificationService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function adminIndex(Request $request)
    {
        $this->ensureAdmin($request);

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
        $this->ensureAdmin($request);

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

    protected function ensureAdmin(Request $request): void
    {
        abort_unless($request->user()?->isAdmin() || $request->user()?->isSuperAdmin(), 403);
    }
}
