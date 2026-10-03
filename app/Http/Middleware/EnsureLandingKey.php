<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureLandingKey
{
    public function handle(Request $request, Closure $next)
    {
        abort_unless(
            hash_equals((string) config('services.landing.key'), (string) $request->header('X-Api-Key')),
            401
        );

        return $next($request);
    }
}
