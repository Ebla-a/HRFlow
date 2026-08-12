<?php

namespace Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; // تأكد من استدعاء Log
use Illuminate\Support\Str;

class EnsureApiHeader
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // 1. Read the passed Correlation ID or Request ID, or generate a new UUID
        $correlationId = $request->header('X-Correlation-ID')
            ?? $request->header('X-Request-ID')
            ?? (string) Str::uuid();

        // 2. Set the header in the Request so it is available to subsequent Middlewares and Controllers
        $request->headers->set('X-Correlation-ID', $correlationId);
        $request->headers->set('X-Request-ID', $correlationId);

        // 3. Share the Correlation ID with Laravel's logging context safely
        Log::withContext([
            'correlation_id' => $correlationId,
        ]);

        $response = $next($request);

        // 4. Attach the header to the Response sent back to the Client
        $response->headers->set('X-Correlation-ID', $correlationId);
        $response->headers->set('X-Request-ID', $correlationId);

        return $response;
    }
}