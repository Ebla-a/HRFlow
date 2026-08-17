<?php
namespace Modules\Auth\App\Exceptions;

use Exception;

class InactiveUserException extends Exception
{
    public function __construct($message = null, $code = 403, ?\Throwable $previous = null)
    {
        
        $message = $message ?: __('User account is inactive.');
        
        parent::__construct($message, $code, $previous);
    }
}
