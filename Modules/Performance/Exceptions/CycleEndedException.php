<?php
namespace Modules\Performance\Exceptions;
use Exception;
use Illuminate\Http\JsonResponse;

class CycleEndedException extends Exception
{
    public function render($request): JsonResponse
    {
        return response()->json([
            'status'  => false,
            'message' => $this->getMessage(),
        ], 422);
    }
}