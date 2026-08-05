<?php

declare(strict_types=1);

namespace Modules\Report\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Modules\Payroll\Events\PayrollFinalized;
use Modules\Report\Services\PayrollReportService;
use Modules\Report\Services\ReportSummaryStoreService;

class UpdatePayrollReportsSummary implements ShouldQueue
{
    public $tries = 3;

    public function __construct(
        private readonly PayrollReportService $payrollReportService,
        private readonly ReportSummaryStoreService $storeService
    ) {}

    /**
     * Handle the "payroll finalized" event.
     *
     * @param PayrollFinalized $event
     * @return void
     */
    public function handle(PayrollFinalized $event): void
    {
        $payrollRun = $event->payrollRun;

        $data = $this->payrollReportService->build($payrollRun);

        $this->storeService->store(
            'payroll',
            $payrollRun->month,
            $payrollRun->year,
            $data
        );

        Log::info("Payroll report summary generated for {$payrollRun->year}-{$payrollRun->month}", [
            'payroll_run_id' => $payrollRun->id,
        ]);
    }
}
