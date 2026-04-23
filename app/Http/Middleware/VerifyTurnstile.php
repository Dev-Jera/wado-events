<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class VerifyTurnstile
{
    public function handle(Request $request, Closure $next): Response
    {
        $secret = (string) config('services.turnstile.secret_key', '');

        if ($secret === '') {
            return $next($request);
        }

        $token = (string) $request->input('cf-turnstile-response', '');

        $result = Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
            'secret'   => $secret,
            'response' => $token,
            'remoteip' => $request->ip(),
        ]);

        if (! ($result->json('success') === true)) {
            return back()->withErrors(['captcha' => 'Security check failed. Please try again.'])->withInput();
        }

        return $next($request);
    }
}
