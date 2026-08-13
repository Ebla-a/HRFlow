<?php

namespace Modules\Payroll\App\Actions\Payslip;

use Modules\Payroll\Models\Payslip;
use Illuminate\Support\Collection;
use Modules\Payroll\Entities\Payslip as EntitiesPayslip;

class GeneratePayslipAction
{
    public function execute($payslipId)
    {
        $payslip = EntitiesPayslip::findOrFail($payslipId);

        if (is_string($payslip->deductions)) {
            $payslip->deductions = collect(json_decode($payslip->deductions, true));
        } elseif (is_array($payslip->deductions)) {
            $payslip->deductions = collect($payslip->deductions);
        } elseif (!($payslip->deductions instanceof Collection)) {
            $payslip->deductions = collect();
        }

        $payslip->deductions = $payslip->deductions->map(function ($deduction) {
            return is_array($deduction) ? (object) $deduction : $deduction;
        });

        return $payslip;
    }
}