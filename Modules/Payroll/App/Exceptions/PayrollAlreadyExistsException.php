<?php

namespace Modules\Payroll\App\Exceptions;

use Exception;

final class PayrollAlreadyExistsException extends Exception
{
    public function __construct()
    {
        parent::__construct('Payroll run already exists for the selected month.');
    }
}