<?php

namespace Modules\Payroll\App\Actions\Payslip;

use Illuminate\Support\Facades\DB;
use Modules\Payroll\App\DTOs\DeductionDTO;
use Modules\Payroll\Entities\Payslip;
use Modules\Payroll\Entities\PayslipDeduction;

final class AddPayslipDeductionAction
{
    public function execute(Payslip $payslip, DeductionDTO $dto): Payslip
    {
        if ($payslip->payrollRun->isFinalized()) {
            throw new \DomainException('Cannot add deductions to a finalized payslip.');
        }

        return DB::transaction(function () use ($payslip, $dto) {
            PayslipDeduction::create([
                'payslip_id' => $payslip->id,
                'type' => $dto->type,
                'amount' => $dto->amount,
                'description' => $dto->description,
            ]);

            $totalDeductions = $payslip->deductions()->sum('amount');
            $newNetSalary = max(0, $payslip->gross_salary - $totalDeductions - $payslip->unpaid_leave_deduction);

            $payslip->update([
                'deductions' => $totalDeductions,
                'net_salary' => $newNetSalary,
            ]);

            return $payslip->fresh();
        });
    }
}