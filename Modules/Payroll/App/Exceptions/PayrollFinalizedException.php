<?php

declare(strict_types=1);

namespace Modules\Payroll\App\Exceptions;

use DomainException;

final class PayrollFinalizedException extends DomainException
{
    public static function alreadyFinalized(int $payrollRunId): self
    {
        return new self("Payroll run with ID {$payrollRunId} is already finalized and cannot be modified.");
    }
}