<?php

declare(strict_types=1);

namespace Modules\Report\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Modules\Employee\Events\EmployeeHired;
use Modules\Employee\Events\EmployeeTerminated;
use Modules\Report\Services\EmployeeReportService;
use Modules\Report\Services\ReportSummaryStoreService;

class UpdateEmployeeReportsSummary implements ShouldQueue
{
    public $tries = 3;

    public function __construct(
        private readonly EmployeeReportService $employeeReportService,
        private readonly ReportSummaryStoreService $storeService
    ) {}

    /**
     * Handle employee hired / terminated events.
     *
     * @param EmployeeHired|EmployeeTerminated $event
     * @return void
     */
    public function handle(EmployeeHired|EmployeeTerminated $event): void
    {
        $employee = $event->employee;

        $date = $employee->hire_date ?? $employee->termination_date ?? now();

        $month = (int) $date->format('n');
        $year = (int) $date->format('Y');

        $data = $this->employeeReportService->build($month, $year);

        $this->storeService->store('employees', $month, $year, $data);

        Log::info("Employee report summary generated for {$year}-{$month}");
    }
}
