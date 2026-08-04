<?php

namespace Modules\Payroll\App\Actions\SalaryStructure;

use Modules\Payroll\App\DTOs\CreateSalaryStructureDTO;
use Modules\Payroll\Entities\SalaryStructure;

final class CreateSalaryStructureAction
{
    public function execute(CreateSalaryStructureDTO $dto): SalaryStructure
    {
        return SalaryStructure::create([
            'employee_id' => $dto->employee_id,
            'basic_salary' => $dto->basic_salary,
            'housing_allowance' => $dto->housing_allowance,
            'transport_allowance' => $dto->transport_allowance,
            'other_allowance' => $dto->other_allowance,
            'effective_date' => $dto->effective_date,
        ]);
    }
}