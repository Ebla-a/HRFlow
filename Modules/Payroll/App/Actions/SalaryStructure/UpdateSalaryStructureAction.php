<?php

namespace Modules\Payroll\App\Actions\SalaryStructure;

use Illuminate\Support\Facades\DB;
use Modules\Payroll\App\DTOs\UpdateSalaryStructureDTO;
use Modules\Payroll\Entities\SalaryStructure;
use Modules\Payroll\Services\SalaryHistoryService;

final readonly class UpdateSalaryStructureAction
{
    public function __construct(
        private SalaryHistoryService $salaryHistoryService,
    ) {}

    public function execute(
        SalaryStructure $salaryStructure,
        UpdateSalaryStructureDTO $dto,
        int $changedBy,
    ): SalaryStructure {

        return DB::transaction(function () use (
            $salaryStructure,
            $dto,
            $changedBy,
        ) {

            $this->salaryHistoryService->store(
                salaryStructure: $salaryStructure,
                dto: $dto,
                changedBy: $changedBy,
            );

            $salaryStructure->update([
                'basic_salary' => $dto->basic_salary,
                'housing_allowance' => $dto->housing_allowance,
                'transport_allowance' => $dto->transport_allowance,
                'other_allowance' => $dto->other_allowance,
                'effective_date' => $dto->effective_date,
            ]);

            return $salaryStructure->fresh();
        });
    }
}