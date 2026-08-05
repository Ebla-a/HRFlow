<?php

declare(strict_types=1);

namespace Modules\Report\Services;

use Modules\Payroll\Entities\PayrollRun;

final class PayrollReportService
{
    /**
     * Build a payroll report summary for a finalized payroll run.
     *
     * @return array<string, mixed>
     */
    public function build(PayrollRun $payrollRun): array
    {
        $payslips = $payrollRun->payslips;

        return [
            'payroll_run_id' => $payrollRun->id,
            'period' => "{$payrollRun->year}-{$payrollRun->month}",
            'month' => $payrollRun->month,
            'year' => $payrollRun->year,
            'status' => $payrollRun->status,
            'total_employees' => $payslips->count(),
            'totals' => [
                'basic_salary' => $payslips->sum('basic_salary'),
                'housing_allowance' => $payslips->sum('housing_allowance'),
                'transport_allowance' => $payslips->sum('transport_allowance'),
                'other_allowance' => $payslips->sum('other_allowance'),
                'gross_salary' => $payslips->sum('gross_salary'),
                'deductions' => $payslips->sum('deductions'),
                'unpaid_leave_deduction' => $payslips->sum('unpaid_leave_deduction'),
                'net_salary' => $payslips->sum('net_salary'),
            ],
            'generated_at' => now()->toDateTimeString(),
        ];
    }
}
