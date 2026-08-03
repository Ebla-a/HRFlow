<?php

namespace Modules\Employee\App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class InvalidJobTitleForDepartmentException extends Exception
{
    public function __construct(
        string $message = 'The selected job title does not belong to the specified department.',
        int $code = Response::HTTP_UNPROCESSABLE_ENTITY
    ) {
        parent::__construct($message, $code);
    }
}