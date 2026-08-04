<?php
namespace Modules\Payroll\Services;

use Illuminate\Support\Facades\Cache;
use Modules\Payroll\Entities\PayrollRun;

final class GetPayrollSummaryService
{
    public function getSummary(PayrollRun $payrollRun): array
    {
        $cacheKey = "payroll_summary_{$payrollRun->year}_{$payrollRun->month}";

        return Cache::remember($cacheKey, now()->addHours(12), function () use ($payrollRun) {
            $payslips = $payrollRun->payslips;

            return [
                'payroll_run_id' => $payrollRun->id,
                'period' => "{$payrollRun->year}-{$payrollRun->month}",
                'status' => $payrollRun->status,
                'total_employees' => $payslips->count(),
                'totals' => [
                    'basic_salary' => $payslips->sum('basic_salary'),
                    'housing_allowance' => $payslips->sum('housing_allowance'),
                    'transport_allowance' => $payslips->sum('transport_allowance'),
                    'other_allowance' => $payslips->sum('other_allowance'),
                    'gross_salary' => $payslips->sum('gross_salary'),
                    'unpaid_leave_deductions' => $payslips->sum('unpaid_leave_deduction'),
                    'manual_deductions' => $payslips->sum('deductions'),
                    'net_salary' => $payslips->sum('net_salary'),
                ],
            ];
        });
    }
}