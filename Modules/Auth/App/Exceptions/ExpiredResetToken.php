<?php
namespace Modules\Auth\App\Exceptions;

use Exception;

class ExpiredResetToken extends Exception{

  protected $code = 400;
    protected $message = 'Invalid or expired reset token';
}
