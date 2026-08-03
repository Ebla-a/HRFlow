<?php

namespace Modules\Payroll\App\Actions;

use Modules\Payroll\App\DTOs\CreatePayrollRunDTO;
use Modules\Payroll\App\Enums\PayrollRunStatus;
use Modules\Payroll\App\Exceptions\PayrollAlreadyExistsException;
use Modules\Payroll\Entities\PayrollRun;

final class CreatePayrollRunAction
{
    /**
     * @throws PayrollAlreadyExistsException
     */
    public function execute(CreatePayrollRunDTO $dto): PayrollRun
    {
        $exists = PayrollRun::query()
            ->where('month', $dto->month)
            ->where('year', $dto->year)
            ->exists();

        if ($exists) {
            throw new PayrollAlreadyExistsException();
        }

        return PayrollRun::create([
            'month' => $dto->month,
            'year' => $dto->year,
            'status' => PayrollRunStatus::Draft,
        ]);
    }
}