<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ── Queue health monitor ────────────────────────────────────────────────────
// Runs every 5 minutes. Warns if jobs are stuck, failed jobs are piling up,
// or Redis is unreachable.
Schedule::call(function () {
    // Stuck jobs: anything older than 10 minutes is a sign workers are down
    try {
        $oldestCreatedAt = DB::table('jobs')->min('created_at');
        if ($oldestCreatedAt !== null) {
            $ageMinutes = (int) round((time() - $oldestCreatedAt) / 60);
            if ($ageMinutes >= 10) {
                Log::warning('Queue monitor: jobs are stuck', [
                    'oldest_job_age_minutes' => $ageMinutes,
                    'pending_count'          => DB::table('jobs')->count(),
                ]);
            }
        }
    } catch (\Throwable $e) {
        Log::error('Queue monitor: could not check jobs table', ['error' => $e->getMessage()]);
    }

    // Failed jobs: alert if count grows beyond threshold
    try {
        $failedCount = DB::table('failed_jobs')->count();
        if ($failedCount > 0) {
            Log::warning('Queue monitor: failed jobs present', ['count' => $failedCount]);
        }
    } catch (\Throwable) {
        // failed_jobs table may not exist in all environments
    }

    // Redis connectivity
    try {
        Redis::ping();
    } catch (\Throwable $e) {
        Log::critical('Queue monitor: Redis is unreachable', ['error' => $e->getMessage()]);
    }
})->everyFiveMinutes()->name('queue:health-monitor')->withoutOverlapping();

// ── Disk space monitor ──────────────────────────────────────────────────────
// Runs hourly. Warns below 20 %, critical below 10 %.
Schedule::call(function () {
    try {
        $free  = disk_free_space(storage_path());
        $total = disk_total_space(storage_path());
        $pct   = $total > 0 ? (int) round(($free / $total) * 100) : 100;

        if ($pct < 10) {
            Log::critical('Disk monitor: critically low disk space', ['free_pct' => $pct]);
        } elseif ($pct < 20) {
            Log::warning('Disk monitor: low disk space', ['free_pct' => $pct]);
        }
    } catch (\Throwable $e) {
        Log::error('Disk monitor: check failed', ['error' => $e->getMessage()]);
    }
})->hourly()->name('disk:health-monitor')->withoutOverlapping();
