<?php
namespace Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogApiRequest
{
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
     * It is executed during the request cycle and before sending the response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $requestId = $request->header('X-Request-ID');

        Log::info("API Request Started", [
            'request_id' => $requestId,
            'method'     => $request->method(),
            'url'        => $request->fullUrl(),
            'ip'         => $request->ip(),
            'user_id'    => $request->user()?->id,
            'payload'    => $this->maskPayload($request->all()),
        ]);

        return $next($request);
    }

    /**
     * It’s executed automatically by Laravel after the client receives the response
     */
    public function terminate(Request $request, Response $response): void
    {
        Log::info("API Request Finished", [
            'request_id'  => $request->header('X-Request-ID'),
            'status_code' => $response->getStatusCode(),
            'method'      => $request->method(),
            'url'         => $request->fullUrl(),
        ]);
    }

    /**
     * Encryption and protection
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