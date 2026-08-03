<?php

declare(strict_types=1);

namespace Modules\Payroll\Services;

use Modules\Payroll\App\DTOs\SalaryCalculationDTO;
use Modules\Payroll\Entities\SalaryStructure;

final class SalaryCalculatorService
{
    public function calculate(
        SalaryStructure $salaryStructure,
        int $unpaidLeaveDays = 0,
        float $manualDeductions = 0,
    ): SalaryCalculationDTO {

        $grossSalary =
            $salaryStructure->basic_salary
            + $salaryStructure->housing_allowance
            + $salaryStructure->transport_allowance
            + $salaryStructure->other_allowance;

        $unpaidLeaveDeduction =
            ($salaryStructure->basic_salary / 30)
            * $unpaidLeaveDays;

        $netSalary =
            $grossSalary
            - $manualDeductions
            - $unpaidLeaveDeduction;

        return new SalaryCalculationDTO(
            basicSalary: (float) $salaryStructure->basic_salary,
            housingAllowance: (float) $salaryStructure->housing_allowance,
            transportAllowance: (float) $salaryStructure->transport_allowance,
            otherAllowance: (float) $salaryStructure->other_allowance,
            grossSalary: (float) $grossSalary,
            manualDeductions: $manualDeductions,
            unpaidLeaveDeduction: (float) $unpaidLeaveDeduction,
            unpaidLeaveDays: $unpaidLeaveDays,
            netSalary: (float) $netSalary,
        );
    }
}