<?php

namespace App\Jobs;

use App\Models\PaymentTransaction;
use App\Services\Payment\PaymentLifecycleService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ExpirePendingPayment implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $paymentTransactionId)
    {
    }

    public function handle(PaymentLifecycleService $lifecycleService): void
    {
        $payment = PaymentTransaction::query()->find($this->paymentTransactionId);

        if (! $payment) {
            return;
        }

        $lifecycleService->expireIfTimedOut($payment);
    }
}
