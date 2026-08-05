<?php

namespace Modules\Payroll\App\Exceptions;

use Exception;

final class SalaryStructureNotFoundException extends Exception
{
    public function __construct(string|int $employeeId)
    {
        parent::__construct(
            "Salary structure not found for employee #{$employeeId}."
        );
    }
}