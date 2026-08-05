<?php

namespace Modules\Payroll\App\Actions\Payslip;

use Modules\Payroll\Entities\Payslip;

final class GeneratePayslipAction
{
    public function execute(Payslip $payslip): array
    {
        $payslip->loadMissing([
            'employee.department',
            'employee.jobTitle',
            'payrollRun',
            'deductions'
        ]);

        return [
            'payslip_id' => $payslip->id,
            'period' => [
                'month' => $payslip->payrollRun->month,
                'year' => $payslip->payrollRun->year,
            ],
            'employee' => [
                'id' => $payslip->employee->id,
                'full_name' => $payslip->employee->full_name,
                'employee_number' => $payslip->employee->employee_number,
                'department' => $payslip->employee->department?->name,
                'job_title' => $payslip->employee->jobTitle?->name,
            ],
            'allowances' => [
                'basic_salary' => $payslip->basic_salary,
                'housing_allowance' => $payslip->housing_allowance,
                'transport_allowance' => $payslip->transport_allowance,
                'other_allowance' => $payslip->other_allowance,
                'gross_salary' => $payslip->gross_salary,
            ],
            'deductions' => [
                'unpaid_leave_days' => $payslip->unpaid_leave_days,
                'unpaid_leave_deduction' => $payslip->unpaid_leave_deduction,
                'manual_deductions_total' => $payslip->deductions,
                'items' => $payslip->deductions->map(fn ($item) => [
                    'id' => $item->id,
                    'type' => $item->type,
                    'amount' => $item->amount,
                    'description' => $item->description,
                ]),
            ],
            'net_salary' => $payslip->net_salary,
            'status' => $payslip->payrollRun->status,
        ];
    }
}