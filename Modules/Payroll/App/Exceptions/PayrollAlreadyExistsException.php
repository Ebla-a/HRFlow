<?php

namespace Modules\Payroll\App\Exceptions;

use Exception;
use Modules\Payroll\App\DTOs\PayrollRunDTO;

final class PayrollAlreadyExistsException extends Exception
{
    public function __construct(?PayrollRunDTO $dto = null)
    {
        $message = $dto 
            ? "Payroll run already exists for {$dto->year}-{$dto->month} selected month" 
            : "Payroll run already exists for the selected month";

        parent::__construct($message);
    }
}