<?php

declare(strict_types=1);

namespace Modules\Payroll\App\Services;

use Illuminate\Support\Collection;
use Modules\Payroll\App\DTOs\UpdateSalaryStructureDTO;
use Modules\Payroll\App\Enums\SalaryField;
use Modules\Payroll\Entities\SalaryHistory;
use Modules\Payroll\Entities\SalaryHistoryItem;
use Modules\Payroll\Entities\SalaryStructure;

final class SalaryHistoryService
{
    /**
     * @var array<string, SalaryField>
     */
    private const FIELD_MAP = [
        'basic_salary' => SalaryField::BasicSalary,
        'housing_allowance' => SalaryField::HousingAllowance,
        'transport_allowance' => SalaryField::TransportAllowance,
        'other_allowance' => SalaryField::OtherAllowance,
    ];

    public function store(
        SalaryStructure $salaryStructure,
        UpdateSalaryStructureDTO $dto,
        int $changedBy,
    ): void {

        $history = SalaryHistory::create([
            'employee_id' => $salaryStructure->employee_id,
            'reason' => $dto->reason,
            'effective_date' => $dto->effective_date,
            'changed_by' => $changedBy,
        ]);

        $this->historyItems(
            salaryStructure: $salaryStructure,
            dto: $dto,
        )
            ->each(fn (array $item) => SalaryHistoryItem::create([
                'salary_history_id' => $history->id,
                'field' => $item['field'],
                'old_amount' => $item['old_amount'],
                'new_amount' => $item['new_amount'],
            ]));
    }

    /**
     * @return Collection<int,array{
     *     field:SalaryField,
     *     old_amount:float,
     *     new_amount:float
     * }>
     */
    private function historyItems(
        SalaryStructure $salaryStructure,
        UpdateSalaryStructureDTO $dto,
    ): Collection {

        $old = [
            'basic_salary' => $salaryStructure->basic_salary,
            'housing_allowance' => $salaryStructure->housing_allowance,
            'transport_allowance' => $salaryStructure->transport_allowance,
            'other_allowance' => $salaryStructure->other_allowance,
        ];

        $new = [
            'basic_salary' => $dto->basic_salary,
            'housing_allowance' => $dto->housing_allowance,
            'transport_allowance' => $dto->transport_allowance,
            'other_allowance' => $dto->other_allowance,
        ];

        return collect(self::FIELD_MAP)
            ->map(function (SalaryField $field, string $key) use ($old, $new) {

                return [
                    'field' => $field,
                    'old_amount' => (float) $old[$key],
                    'new_amount' => (float) $new[$key],
                ];

            })
            ->filter(fn (array $item) => $item['old_amount'] !== $item['new_amount'])
            ->values();
    }
}