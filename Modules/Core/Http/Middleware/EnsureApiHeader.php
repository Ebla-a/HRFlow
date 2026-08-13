<?php

declare(strict_types=1);

namespace Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class EnsureApiHeader
{
    /**
     * Handle an incoming request, ensure correlation headers exist, 
     * and attach them to the logs and response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Capture the existing Correlation/Request ID or generate a new UUID if missing
        $correlationId = $request->header('X-Correlation-ID')
            ?? $request->header('X-Request-ID')
            ?? (string) Str::uuid();

        // 2. Inject the correlation ID back into the request headers for subsequent layers
        $request->headers->set('X-Correlation-ID', $correlationId);
        $request->headers->set('X-Request-ID', $correlationId);

        // 3. Bind the correlation ID to Laravel's global logging context
        Log::withContext([
            'correlation_id' => $correlationId,
        ]);

        $response = $next($request);

        // 4. Attach the correlation ID to the outgoing response headers for client tracking
        $response->headers->set('X-Correlation-ID', $correlationId);
        $response->headers->set('X-Request-ID', $correlationId);

        return $response;
    }
}