<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);

        $trustedProxies = array_filter(array_map('trim', explode(',', (string) env('TRUSTED_PROXIES', implode(',', [
            '127.0.0.1',
            '::1',
            '10.0.0.0/8',
            '172.16.0.0/12',
            '192.168.0.0/16',
        ])))));

        // Restrict proxy trust to known trusted proxies to prevent IP spoofing via X-Forwarded-* headers.
        // This prevents attackers from manipulating client IP for rate limiting bypass or security logging evasion.
        $middleware->trustProxies(
            at: $trustedProxies,
            headers: Request::HEADER_X_FORWARDED_FOR
                | Request::HEADER_X_FORWARDED_HOST
                | Request::HEADER_X_FORWARDED_PORT
                | Request::HEADER_X_FORWARDED_PROTO
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Redirect unauthenticated scanner users to the Filament login
        // (gate staff don't have a public-site account login)
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, Request $request) {
            if ($request->is('ticket-verification*') && ! $request->expectsJson()) {
                session()->put('url.intended', $request->fullUrl());
                return redirect()->route('filament.admin.auth.login');
            }
        });
    })->create();
