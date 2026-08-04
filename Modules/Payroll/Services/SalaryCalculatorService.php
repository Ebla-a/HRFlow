<?php
namespace Modules\Payroll\Services;

use Modules\Payroll\App\DTOs\PayrollCalculationDTO;
use Modules\Payroll\Entities\SalaryStructure;

final class SalaryCalculatorService
{
    public function calculate(
        SalaryStructure $salaryStructure,
        int $unpaidLeaveDays = 0,
        float $manualDeductions = 0.0
    ): PayrollCalculationDTO {
        $basicSalary = $salaryStructure->basic_salary;
        $housing = $salaryStructure->housing_allowance;
        $transport = $salaryStructure->transport_allowance;
        $other = $salaryStructure->other_allowance;

        $grossSalary = $basicSalary + $housing + $transport + $other;

      // Calculate unpaid days deduction based on basic salary
        $unpaidLeaveDeduction = ($basicSalary / 30) * $unpaidLeaveDays;

        // Protecting the salary from going into negative values 
        $netSalary = max(0, $grossSalary - $manualDeductions - $unpaidLeaveDeduction);

        return new PayrollCalculationDTO(
            basicSalary: $basicSalary,
            housingAllowance: $housing,
            transportAllowance: $transport,
            otherAllowance: $other,
            grossSalary: $grossSalary,
            unpaidLeaveDeduction: round($unpaidLeaveDeduction, 2),
            unpaidLeaveDays: $unpaidLeaveDays,
            netSalary: round($netSalary, 2)
        );
    }
}