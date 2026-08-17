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
        ?string $message = null,
        int $code = Response::HTTP_UNPROCESSABLE_ENTITY
    ) {
        $message = $message ?: __('The selected manager is invalid or does not belong to the eligible department.');

        parent::__construct($message, $code);
    }
}