<?php

namespace Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;


class EnsureApiHeader
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->hasHeader('X-Request-ID')) {
            $request->headers->set('X-Request-ID', (string) \Illuminate\Support\Str::uuid());
        }

        $response = $next($request);
        $response->headers->set('X-Request-ID', $request->header('X-Request-ID'));

        return $response;
    }
}