<?php

namespace Modules\Employee\App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class InvalidJobTitleForDepartmentException extends Exception
{
    public function __construct(
    ?string $message = null,
    int $code = Response::HTTP_UNPROCESSABLE_ENTITY
) {
    $message = $message ?: __('The selected job title does not belong to the specified department.');

    parent::__construct($message, $code);
}
}