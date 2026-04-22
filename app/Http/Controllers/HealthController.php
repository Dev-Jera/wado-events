<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class HealthController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $checks = [];
        $httpStatus = 200;

        // ── Database ────────────────────────────────────────────────────
        try {
            DB::selectOne('SELECT 1 AS ok');
            $checks['database'] = 'ok';
        } catch (\Throwable $e) {
            $checks['database'] = 'fail: ' . $e->getMessage();
            $httpStatus = 503;
        }

        // ── Redis ───────────────────────────────────────────────────────
        try {
            Redis::ping();
            $checks['redis'] = 'ok';
        } catch (\Throwable $e) {
            $checks['redis'] = 'fail: ' . $e->getMessage();
            $httpStatus = 503;
        }

        // ── Queue worker ─────────────────────────────────────────────────
        // A job older than 10 minutes still in the queue means no worker is running.
        try {
            $oldestCreatedAt = DB::table('jobs')->min('created_at');
            if ($oldestCreatedAt !== null) {
                $ageMinutes = (int) round((time() - $oldestCreatedAt) / 60);
                if ($ageMinutes >= 10) {
                    $checks['queue'] = "warn: oldest pending job is {$ageMinutes} min old — worker may be down";
                    $httpStatus = max($httpStatus, 503);
                } else {
                    $checks['queue'] = "ok ({$ageMinutes} min oldest job)";
                }
            } else {
                $checks['queue'] = 'ok (no pending jobs)';
            }
        } catch (\Throwable) {
            $checks['queue'] = 'ok (jobs table unavailable)';
        }

        // ── Disk space ──────────────────────────────────────────────────
        try {
            $free  = disk_free_space(storage_path());
            $total = disk_total_space(storage_path());
            $pct   = $total > 0 ? (int) round(($free / $total) * 100) : 100;

            $checks['disk'] = $pct . '% free';

            if ($pct < 10) {
                $httpStatus = max($httpStatus, 503);
            }
        } catch (\Throwable) {
            $checks['disk'] = 'unavailable';
        }

        // ── Cache round-trip ─────────────────────────────────────────────
        try {
            $probe = 'health:probe:' . time();
            Cache::put($probe, 1, 5);
            Cache::forget($probe);
            $checks['cache'] = 'ok';
        } catch (\Throwable $e) {
            $checks['cache'] = 'fail: ' . $e->getMessage();
            $httpStatus = max($httpStatus, 503);
        }

        return response()->json([
            'status'    => $httpStatus === 200 ? 'ok' : 'degraded',
            'checks'    => $checks,
            'timestamp' => now()->toIso8601String(),
        ], $httpStatus);
    }
}
