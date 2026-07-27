<?php 
namespace Modules\Core\App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class ApiExceptionHandler extends ExceptionHandler{

public function render($request , Throwable $e)
{

        return response()->json([
            'status' => false,
            'message' => $e->getMessage(),
        ], 500);
}

}
    