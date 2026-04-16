<?php

namespace App\Services\Admin;

use App\Models\LoginAttempt;
use App\Models\PaymentTransaction;
use App\Models\TicketScanLog;
use App\Models\User;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;

class AdminIncidentNotificationService
{
    public function notifyFailedTicketScan(TicketScanLog $scanLog, ?int $selectedEventId = null): void
    {
        $recipients = $this->adminRecipients();

        if ($recipients->isEmpty()) {
            return;
        }

        $eventId = $selectedEventId ?: $scanLog->ticket?->event_id;
        $gateUser = $scanLog->staff?->name ?: 'Unknown staff';
        $ticketCode = $scanLog->ticket_code ?: 'N/A';

        $message = sprintf(
            'Result: %s. Ticket: %s. Staff: %s. Reason: %s',
            strtoupper((string) $scanLog->result),
            $ticketCode,
            $gateUser,
            (string) $scanLog->message
        );

        $notification = Notification::make()
            ->warning()
            ->title('Failed ticket scan detected')
            ->body($message)
            ->actions([
                Action::make('open_scanner')
                    ->label('Open scanner')
                    ->url($this->scannerUrl($eventId), shouldOpenInNewTab: false),
            ]);

        $notification->sendToDatabase($recipients);
    }

    public function notifyFailedPayment(PaymentTransaction $payment, string $reason): void
    {
        $recipients = $this->adminRecipients();

        if ($recipients->isEmpty()) {
            return;
        }

        $eventTitle = $payment->event?->title ?: ('Event #' . $payment->event_id);
        $customerName = $payment->user?->name ?: ('User #' . $payment->user_id);

        $message = sprintf(
            'Payment #%d failed for %s (%s). Amount: %s %s. Reason: %s',
            $payment->id,
            $customerName,
            $eventTitle,
            (string) $payment->currency,
            number_format((float) $payment->total_amount, 2),
            $reason
        );

        $notification = Notification::make()
            ->danger()
            ->title('Payment failed')
            ->body($message)
            ->actions([
                Action::make('open_payments')
                    ->label('Open payments')
                    ->url($this->paymentsUrl(), shouldOpenInNewTab: false),
            ]);

        $notification->sendToDatabase($recipients);
    }

    public function notifySuspiciousLoginActivity(LoginAttempt $attempt, int $failureCount): void
    {
        $recipients = $this->adminRecipients();

        if ($recipients->isEmpty()) {
            return;
        }

        $message = sprintf(
            '%d failed login attempts from IP %s in the last 10 minutes. Last email tried: %s.',
            $failureCount,
            $attempt->ip_address,
            $attempt->email
        );

        $notification = Notification::make()
            ->danger()
            ->title('Suspicious login activity detected')
            ->body($message);

        $notification->sendToDatabase($recipients);
    }

    protected function adminRecipients(): Collection
    {
        return User::query()
            ->where('role', 'super_admin')
            ->get();
    }

    protected function scannerUrl(?int $eventId): string
    {
        if ($eventId) {
            return route('tickets.verify.index', ['event_id' => $eventId]);
        }

        return route('tickets.verify.index');
    }

    protected function paymentsUrl(): string
    {
        return route('filament.admin.resources.payment-transactions.index');
    }
}
