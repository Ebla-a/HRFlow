<?php

namespace Modules\Employee\App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureApiHeader
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->hasHeader('X-Request-ID')) {
            $request->headers->set('X-Request-ID', (string) \Illuminate\Support\Str::uuid());
        }

        $response = $next($request);
        $response->headers->set('X-Request-ID', $request->header('X-Request-ID'));

        return $response;
    }
}