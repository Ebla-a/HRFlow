<?php

declare(strict_types=1);

namespace Modules\Payroll\App\Actions\PayrollRun;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Modules\Payroll\App\Exceptions\PayrollFinalizedException;
use Modules\Payroll\Entities\PayrollRun;
use Modules\Payroll\Events\PayrollFinalized;

final class FinalizePayrollRunAction
{
    public function execute(PayrollRun $payrollRun, int $finalizedBy): PayrollRun
    {
        if ($payrollRun->isFinalized()) {
            throw new PayrollFinalizedException('This payroll run is already finalized.');
        }

        return DB::transaction(function () use ($payrollRun, $finalizedBy) {
            $payrollRun->markAsFinalized($finalizedBy);

            Cache::forget("payroll_summary_{$payrollRun->year}_{$payrollRun->month}");

            event(new PayrollFinalized($payrollRun));

            return $payrollRun;
        });
    }
}