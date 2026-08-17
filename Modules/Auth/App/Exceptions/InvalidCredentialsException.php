<?php
namespace Modules\Auth\App\Exceptions;

use Exception;

class InvalidCredentialsException extends Exception
{   
    public function __construct($message = null, $code = 401, ?\Throwable $previous = null)
    {
            
            $message = $message ?: __('Invalid password.');
            
            parent::__construct($message, $code, $previous);
    }
}
