<?php

namespace Modules\Payroll\App\Actions\Payslip;

use Modules\Payroll\Entities\Payslip;
use Modules\Payroll\Services\SalaryCalculatorService;

final readonly class CalculatePayslipAction
{
    public function __construct(
        private SalaryCalculatorService $calculatorService
    ) {}

    public function execute(Payslip $payslip): Payslip
    {
        if ($payslip->payrollRun->isFinalized()) {
            throw new \DomainException('Cannot recalculate a payslip from a finalized payroll run.');
        }

        $employee = $payslip->employee()->with('salaryStructure')->first();

        if (!$employee?->salaryStructure) {
            throw new \DomainException("Employee {$payslip->employee_id} does not have an active salary structure.");
        }

        $manualDeductions = $payslip->deductions()->sum('amount');

        $calculation = $this->calculatorService->calculate(
            salaryStructure: $employee->salaryStructure,
            unpaidLeaveDays: $payslip->unpaid_leave_days,
            manualDeductions: $manualDeductions
        );

        $payslip->update([
            'basic_salary' => $calculation->basicSalary,
            'housing_allowance' => $calculation->housingAllowance,
            'transport_allowance' => $calculation->transportAllowance,
            'other_allowance' => $calculation->otherAllowance,
            'gross_salary' => $calculation->grossSalary,
            'deductions' => $manualDeductions,
            'unpaid_leave_deduction' => $calculation->unpaidLeaveDeduction,
            'net_salary' => $calculation->netSalary,
        ]);

        return $payslip->fresh();
    }
}