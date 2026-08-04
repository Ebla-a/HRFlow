<?php

namespace Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogApiRequest
{
    public function handle(Request $request, Closure $next): Response
    {
        $requestId = $request->header('X-Request-ID');

        Log::info("API Request Started", [
            'request_id' => $requestId,
            'method'     => $request->method(),
            'url'        => $request->fullUrl(),
            'ip'         => $request->ip(),
            'user_id'    => $request->user()?->id,
            'payload'    => $request->except(['password', 'password_confirmation']),
        ]);

        /** @var Response $response */
        $response = $next($request);

        Log::info("API Request Finished", [
            'request_id'  => $requestId,
            'status_code' => $response->getStatusCode(),
            'method'      => $request->method(),
            'url'         => $request->fullUrl(),
        ]);

        return $response;
    }
}