<?php

namespace App\Providers;

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
    }
}
