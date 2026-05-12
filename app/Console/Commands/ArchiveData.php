<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class ArchiveData extends Command
{
    protected $signature = 'archive:data {--dry-run}';

    protected $description = 'Move old terminal data from the live database into the archive database.';

    public function handle(): int
    {
        $cutoffMonths = max(1, (int) config('archive.cutoff_months', 6));
        $errorDays = max(30, (int) config('archive.error_days', 90));
        $chunkSize = max(50, (int) config('archive.chunk_size', 500));
        $dryRun = (bool) $this->option('dry-run');

        try {
            $this->ensureArchiveTablesExist();

            $movedPayments = $this->archivePaymentTransactions($cutoffMonths, $chunkSize, $dryRun);
            $movedTickets = $this->archiveTickets($cutoffMonths, $chunkSize, $dryRun);
            $movedErrors = $this->archiveErrorLogs($errorDays, $chunkSize, $dryRun);

            $this->info(sprintf(
                'Archive completed%s: payments=%d, tickets=%d, error_logs=%d',
                $dryRun ? ' (dry-run)' : '',
                $movedPayments,
                $movedTickets,
                $movedErrors
            ));

            Log::info('Archive job completed', [
                'dry_run' => $dryRun,
                'payments' => $movedPayments,
                'tickets' => $movedTickets,
                'error_logs' => $movedErrors,
            ]);

            return self::SUCCESS;
        } catch (\Throwable $e) {
            Log::error('Archive job failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $this->error('Archive failed: ' . $e->getMessage());

            return self::FAILURE;
        }
    }

    private function archivePaymentTransactions(int $cutoffMonths, int $chunkSize, bool $dryRun): int
    {
        $cutoff = Carbon::now()->subMonths($cutoffMonths);
        $terminalStatuses = [
            'confirmed',
            'failed',
            'refunded',
            'expired',
        ];

        $baseQuery = DB::connection('mysql')
            ->table('payment_transactions')
            ->where('created_at', '<', $cutoff)
            ->whereIn('status', $terminalStatuses);

        return $this->moveRowsInBatches(
            $baseQuery,
            'id',
            'payment_transactions',
            $chunkSize,
            $dryRun
        );
    }

    private function archiveTickets(int $cutoffMonths, int $chunkSize, bool $dryRun): int
    {
        $cutoff = Carbon::now()->subMonths($cutoffMonths);

        $baseQuery = DB::connection('mysql')
            ->table('tickets')
            ->join('events', 'events.id', '=', 'tickets.event_id')
            ->where('events.ends_at', '<', $cutoff)
            ->select('tickets.*');

        return $this->moveRowsInBatches(
            $baseQuery,
            'tickets.id',
            'tickets',
            $chunkSize,
            $dryRun
        );
    }

    private function archiveErrorLogs(int $errorDays, int $chunkSize, bool $dryRun): int
    {
        $cutoff = Carbon::now()->subDays($errorDays);

        $baseQuery = DB::connection('mysql')
            ->table('error_logs')
            ->where('created_at', '<', $cutoff);

        return $this->moveRowsInBatches(
            $baseQuery,
            'id',
            'error_logs',
            $chunkSize,
            $dryRun
        );
    }

    private function moveRowsInBatches(Builder $baseQuery, string $idColumn, string $tableName, int $chunkSize, bool $dryRun): int
    {
        $moved = 0;
        $lastId = 0;
        $idField = str_contains($idColumn, '.') ? substr($idColumn, strrpos($idColumn, '.') + 1) : $idColumn;

        while (true) {
            $rows = (clone $baseQuery)
                ->where($idColumn, '>', $lastId)
                ->orderBy($idColumn)
                ->limit($chunkSize)
                ->get();

            if ($rows->isEmpty()) {
                break;
            }

            $payload = $rows->map(fn ($row) => (array) $row)->all();
            $ids = $rows->pluck($idField)->all();

            if (! $dryRun && $payload !== []) {
                DB::connection('archive')->table($tableName)->insertOrIgnore($payload);
                DB::connection('mysql')->table($tableName)->whereIn('id', $ids)->delete();
            }

            $moved += count($payload);
            $lastId = (int) ($rows->last()->{$idField} ?? $lastId);
        }

        return $moved;
    }

    private function ensureArchiveTablesExist(): void
    {
        if (! Schema::connection('archive')->hasTable('payment_transactions')) {
            Schema::connection('archive')->create('payment_transactions', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('user_id')->index();
                $table->unsignedBigInteger('event_id')->index();
                $table->unsignedBigInteger('ticket_category_id')->index();
                $table->unsignedBigInteger('ticket_id')->nullable()->index();
                $table->string('idempotency_key', 64)->unique();
                $table->string('holder_name')->nullable()->index();
                $table->string('payment_provider', 20)->nullable();
                $table->string('sales_channel', 20)->nullable();
                $table->unsignedBigInteger('collected_by_user_id')->nullable()->index();
                $table->timestamp('collected_at')->nullable();
                $table->string('collector_reference', 120)->nullable();
                $table->string('phone_number', 40)->nullable();
                $table->unsignedBigInteger('promo_code_id')->nullable()->index();
                $table->unsignedInteger('quantity');
                $table->decimal('unit_price', 12, 2);
                $table->decimal('discount_amount', 10, 2)->default(0);
                $table->decimal('total_amount', 12, 2);
                $table->string('currency', 8)->default('UGX');
                $table->string('status', 20)->index();
                $table->string('provider_reference', 120)->nullable()->index();
                $table->string('provider_status', 60)->nullable();
                $table->json('provider_payload')->nullable();
                $table->json('webhook_payload')->nullable();
                $table->timestamp('expires_at')->nullable()->index();
                $table->timestamp('callback_received_at')->nullable();
                $table->timestamp('confirmed_at')->nullable();
                $table->timestamp('failed_at')->nullable();
                $table->timestamp('refunded_at')->nullable();
                $table->timestamp('refund_requested_at')->nullable();
                $table->string('refund_request_status', 40)->nullable();
                $table->text('refund_request_reason')->nullable();
                $table->timestamp('ticket_issued_at')->nullable();
                $table->text('last_error')->nullable();
                $table->softDeletes();
                $table->timestamps();

                $table->index('created_at', 'payment_transactions_created_at_idx');
                $table->index(['event_id', 'status'], 'payment_transactions_event_status_idx');
                $table->index(['user_id', 'status'], 'payment_transactions_user_status_idx');
            });
        }

        if (! Schema::connection('archive')->hasTable('tickets')) {
            Schema::connection('archive')->create('tickets', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('user_id')->index();
                $table->unsignedBigInteger('event_id')->index();
                $table->unsignedBigInteger('ticket_category_id')->index();
                $table->string('holder_name')->nullable();
                $table->string('payer_name')->nullable();
                $table->string('ticket_code')->unique();
                $table->string('qr_code_path')->nullable();
                $table->unsignedInteger('quantity');
                $table->decimal('unit_price', 12, 2)->default(0);
                $table->decimal('total_amount', 12, 2)->default(0);
                $table->string('payment_provider', 20)->default('free');
                $table->string('status', 40)->default('confirmed')->index();
                $table->dateTime('purchased_at')->nullable();
                $table->dateTime('used_at')->nullable();
                $table->string('gate_status', 10)->default('outside');
                $table->unsignedSmallInteger('reentry_count')->default(0);
                $table->timestamp('last_entry_at')->nullable();
                $table->timestamp('last_exit_at')->nullable();
                $table->softDeletes();
                $table->timestamps();

                $table->index(['event_id', 'status'], 'tickets_event_status_idx');
                $table->index(['event_id', 'used_at'], 'tickets_event_used_at_idx');
                $table->index('status', 'tickets_status_idx');
            });
        }

        if (! Schema::connection('archive')->hasTable('error_logs')) {
            Schema::connection('archive')->create('error_logs', function (Blueprint $table): void {
                $table->id();
                $table->string('type')->index();
                $table->string('severity')->index();
                $table->text('message');
                $table->json('context')->nullable();
                $table->string('user_id')->nullable()->index();
                $table->string('payment_id')->nullable()->index();
                $table->string('event_id')->nullable()->index();
                $table->timestamp('resolved_at')->nullable()->index();
                $table->timestamps();

                $table->index(['type', 'created_at'], 'error_logs_type_created_at_idx');
                $table->index(['severity', 'created_at'], 'error_logs_severity_created_at_idx');
            });
        }
    }
}
