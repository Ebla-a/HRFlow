<?php

namespace Modules\Core\App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class UserNotFoundException extends Exception
{
    public function render($request)
    {
        return response()->json([
            'success' => false,
            'message' => $this->getMessage() ?: 'User not found.',
        ], Response::HTTP_NOT_FOUND);
    }
}