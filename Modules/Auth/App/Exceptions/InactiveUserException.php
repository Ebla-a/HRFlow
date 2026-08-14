<?php
namespace Modules\Auth\App\Exceptions;

use Exception;

class InactiveUserException extends Exception
{
    protected $code = 403;
    protected $message = 'User account is inactive.';
}
