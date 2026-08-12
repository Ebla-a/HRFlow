<?php

namespace Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogApiRequest
{
    /**
     * Sensitive payload fields that should be masked in log files.
     */
    protected array $maskedFields = [
        'password',
        'password_confirmation',
        'current_password',
        'token',
        'access_token',
        'secret',
        'iban',
        'national_id',
    ];

    /**
     * Executed during the request cycle before sending the response to the client.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Extract Correlation ID or Request ID from headers
        $tracingId = $request->header('X-Correlation-ID') ?? $request->header('X-Request-ID');

        Log::info("API Request Started", [
            'correlation_id' => $tracingId,
            'request_id'     => $tracingId,
            'method'         => $request->method(),
            'url'            => $request->fullUrl(),
            'ip'             => $request->ip(),
            'user_id'        => $request->user()?->id,
            'payload'        => $this->maskPayload($request->all()),
        ]);

        return $next($request);
    }

    /**
     * Executed automatically by Laravel after the client receives the response.
     */
    public function terminate(Request $request, Response $response): void
    {
        // Extract Correlation ID or Request ID from headers
        $tracingId = $request->header('X-Correlation-ID') ?? $request->header('X-Request-ID');

        Log::info("API Request Finished", [
            'correlation_id' => $tracingId,
            'request_id'     => $tracingId,
            'status_code'    => $response->getStatusCode(),
            'method'         => $request->method(),
            'url'            => $request->fullUrl(),
        ]);
    }

    /**
     * Recursively mask sensitive fields in the input payload.
     */
    protected function maskPayload(array $payload): array
    {
        foreach ($payload as $key => $value) {
            if (is_array($value)) {
                $payload[$key] = $this->maskPayload($value);
            } elseif (in_array(strtolower($key), $this->maskedFields, true)) {
                $payload[$key] = '***MASKED***';
            }
        }

        return $payload;
    }
}