<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;


abstract class Controller
{
    /**
     * Send a success JSON response.
     */
    public  function success(
        mixed $data = null,
        string $message = 'Success',
        int $status = Response::HTTP_OK,
        array $meta = []
    ): JsonResponse {
        $response = [
            'status' => true,
            'message' => $message,
            'data' => $data,
        ];

        if (! empty($meta)) {
            $response['meta'] = $meta;
        }

        return response()->json($response, $status);
    }

    /**
     * Send an error JSON response.
     */
    public  function error(
        string $message = 'Error',
        int $status = Response::HTTP_BAD_REQUEST,
        array $errors = []
    ): JsonResponse {
        return response()->json([
            'status' => false,
            'message' => $message,
            'errors' => (object) $errors,
        ], $status);
    }
}

