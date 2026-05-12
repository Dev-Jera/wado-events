<?php

namespace App\Console\Commands;

use App\Models\ErrorLog;
use App\Models\PaymentTransaction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MonitorApplicationHealth extends Command
{
    protected $signature = 'monitor:health';
    protected $description = 'Check application health: queue backlog, stuck payments, failed jobs, etc.';

    public function handle(): int
    {
        $issues = [];

        // Check queue backlog
        $queueBacklog = DB::table('jobs')->where('queue', 'tickets')->count();
        if ($queueBacklog > 50) {
            $issues[] = [
                'severity' => 'warning',
                'type' => 'queue',
                'message' => "High ticket queue backlog: {$queueBacklog} jobs pending",
                'context' => ['queue_count' => $queueBacklog],
            ];
        }

        // Check stuck pending payments
        $stuckPayments = PaymentTransaction::query()
            ->where('status', PaymentTransaction::STATUS_PENDING)
            ->where('created_at', '<', now()->subHours(2))
            ->count();

        if ($stuckPayments > 0) {
            $issues[] = [
                'severity' => 'warning',
                'type' => 'payment',
                'message' => "{$stuckPayments} payments pending for >2 hours",
                'context' => ['payment_count' => $stuckPayments],
            ];
        }

        // Check failed jobs
        $failedJobs = DB::table('failed_jobs')->count();
        if ($failedJobs > 0) {
            $issues[] = [
                'severity' => 'error',
                'type' => 'queue',
                'message' => "{$failedJobs} jobs have permanently failed",
                'context' => ['failed_job_count' => $failedJobs],
            ];
        }

        // Check for unissued confirmed payments
        $unissuedTickets = PaymentTransaction::query()
            ->where('status', PaymentTransaction::STATUS_CONFIRMED)
            ->whereNull('ticket_id')
            ->where('confirmed_at', '<', now()->subMinutes(30))
            ->count();

        if ($unissuedTickets > 0) {
            $issues[] = [
                'severity' => 'error',
                'type' => 'ticket',
                'message' => "{$unissuedTickets} payments confirmed but ticket not issued after 30 min",
                'context' => ['unissued_count' => $unissuedTickets],
            ];
        }

        // Check recent webhook errors in logs
        $recentErrors = DB::table('error_logs')
            ->where('type', 'webhook')
            ->where('created_at', '>', now()->subHours(1))
            ->count();

        if ($recentErrors > 5) {
            $issues[] = [
                'severity' => 'warning',
                'type' => 'webhook',
                'message' => "{$recentErrors} webhook errors in last hour",
                'context' => ['error_count' => $recentErrors],
            ];
        }

        // Store all issues in error_logs table for dashboard
        if (!empty($issues)) {
            foreach ($issues as $issue) {
                ErrorLog::create([
                    'type' => $issue['type'],
                    'severity' => $issue['severity'],
                    'message' => $issue['message'],
                    'context' => $issue['context'],
                ]);

                Log::channel('monitoring')->log(
                    $issue['severity'] === 'error' ? 'error' : 'warning',
                    $issue['message'],
                    $issue['context']
                );
            }
            
            $this->warn("⚠ Found " . count($issues) . " issues\n");
            foreach ($issues as $issue) {
                $this->line("  [{$issue['severity']}] {$issue['message']}");
            }
        } else {
            Log::channel('monitoring')->info('Health check passed - all systems normal');
            $this->info('✓ All systems healthy');
        }

        return Command::SUCCESS;
    }
}
