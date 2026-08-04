<?php

declare(strict_types=1);

namespace Modules\Payroll\App\Actions\PayrollRun;

use Illuminate\Support\Facades\DB;
use Modules\Employee\Entities\Employee;
use Modules\Payroll\App\Exceptions\SalaryStructureNotFoundException;
use Modules\Payroll\Entities\PayrollRun;
use Modules\Payroll\Entities\Payslip;
use Modules\Payroll\Services\SalaryCalculatorService;

final readonly class ProcessPayrollRunAction
{
    public function __construct(
        private SalaryCalculatorService $calculatorService
    ) {}

    public function execute(PayrollRun $payrollRun, int $processedBy): PayrollRun
    {
        return DB::transaction(function () use ($payrollRun, $processedBy) {

            $payrollRun->payslips()->delete();

            Employee::query()
                ->active()
                ->with(['salaryStructure', 'leaveRequests' => function ($query) use ($payrollRun) {
                    $query->approved()
                        ->unpaid()
                        ->forMonth($payrollRun->month, $payrollRun->year);
                }])
                ->chunkById(200, function ($employees) use ($payrollRun) {
                    $now = now();
                    $payslipsToInsert = [];

                    foreach ($employees as $employee) {
                        if (! $employee->salaryStructure) {
                            throw new SalaryStructureNotFoundException((string) $employee->id);
                        }

                        $unpaidDays = (int) $employee->leaveRequests->sum('days_count');

                        $calculation = $this->calculatorService->calculate(
                            salaryStructure: $employee->salaryStructure,
                            unpaidLeaveDays: $unpaidDays,
                            manualDeductions: 0
                        );

                        $payslipsToInsert[] = [
                            'payroll_run_id' => $payrollRun->id,
                            'employee_id' => $employee->id,
                            'basic_salary' => $calculation->basicSalary,
                            'housing_allowance' => $calculation->housingAllowance,
                            'transport_allowance' => $calculation->transportAllowance,
                            'other_allowance' => $calculation->otherAllowance,
                            'gross_salary' => $calculation->grossSalary,
                            'deductions' => 0,
                            'unpaid_leave_deduction' => $calculation->unpaidLeaveDeduction,
                            'unpaid_leave_days' => $calculation->unpaidLeaveDays,
                            'net_salary' => $calculation->netSalary,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }

                    if (! empty($payslipsToInsert)) {
                        Payslip::insert($payslipsToInsert);
                    }
                });

            $payrollRun->markAsProcessing($processedBy);

            return $payrollRun->fresh(['payslips']);
        });
    }
}