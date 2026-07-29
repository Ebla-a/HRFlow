<?php
namespace Modules\Core\App\Traits;

trait ApiResponseTrait {

protected function success($data = null , $message="Success", $status = 200)
{
    return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
}


    protected function error($message = 'Error', $status = 400, $errors = [])
    {
        return response()->json([
            'status' => false,
            'message' => $message,
            'errors' => $errors,
        ], $status);
    }
}