<?php

namespace App\Providers;

use Illuminate\Database\Connection;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (! app()->environment('local')) {
            URL::forceScheme('https');

            // Force root URL to https — covers asset() and Filament asset generation
            // when APP_URL is set to http:// in Railway environment variables.
            $appUrl = config('app.url', '');
            if ($appUrl && str_starts_with($appUrl, 'http://')) {
                URL::forceRootUrl(str_replace('http://', 'https://', $appUrl));
            }
        }

        $this->registerSlowQueryLogging();
    }

    protected function registerSlowQueryLogging(): void
    {
        // Log any query that takes longer than 500 ms.
        // Raise to 1000 ms in production if logs are too noisy.
        $thresholdMs = (int) config('app.slow_query_threshold_ms', 500);

        DB::whenQueryingForLongerThan($thresholdMs, function (Connection $connection, QueryExecuted $event): void {
            Log::warning('Slow query detected', [
                'sql'        => $event->sql,
                'bindings'   => $event->bindings,
                'time_ms'    => round($event->time, 2),
                'connection' => $connection->getName(),
                'url'        => request()?->fullUrl(),
            ]);
        });
    }
}
