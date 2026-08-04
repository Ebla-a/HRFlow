<?php

declare(strict_types=1);

namespace Modules\Payroll\App\Actions\PayrollRun;

use Illuminate\Support\Facades\DB;
use Modules\Payroll\App\DTOs\PayrollRunDTO;
use Modules\Payroll\App\Enums\PayrollRunStatus;
use Modules\Payroll\App\Exceptions\PayrollAlreadyExistsException;
use Modules\Payroll\Entities\PayrollRun;

final class CreatePayrollRunAction
{
    public function execute(
        PayrollRunDTO $dto,
    ): PayrollRun {

        $exists = PayrollRun::query()
            ->forMonth($dto->month, $dto->year)
            ->exists();

        if ($exists) {
            throw new PayrollAlreadyExistsException();
        }

        return DB::transaction(function () use ($dto): PayrollRun {

            return PayrollRun::create([
                'month' => $dto->month,
                'year' => $dto->year,
                'status' => PayrollRunStatus::Draft,
                'notes' => $dto->notes,
            ]);

        });
    }
}