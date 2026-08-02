<?php

namespace Modules\Employee\App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class InvalidManagerForDepartmentException extends Exception
{
    /**
     * @param string $message
     * @param int $code
     */
    public function __construct(
        string $message = 'The selected manager is invalid or does not belong to the eligible department.',
        int $code = Response::HTTP_UNPROCESSABLE_ENTITY
    ) {
        parent::__construct($message, $code);
    }
}