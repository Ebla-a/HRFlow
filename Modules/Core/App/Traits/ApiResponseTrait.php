<?php

namespace Modules\Core\App\Traits;

trait ApiResponseTrait
{
    /**
     * @param mixed $data
     * @param mixed $message
     * @param mixed $status
     * @param mixed $meta
     * @return \Illuminate\Http\JsonResponse
     */
    protected function success($data = null, $message = "Success", $status = 200, $meta = [])
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data,
            'meta' => $meta,
        ], $status);
    }

    /**
     * @param mixed $message
     * @param mixed $status
     * @param mixed $errors
     * @return \Illuminate\Http\JsonResponse
     */
    protected function error($message = 'Error', $status = 400, $errors = [])
    {
        return response()->json([
            'status' => false,
            'message' => $message,
            'errors' => $errors,
        ], $status);
    }
}