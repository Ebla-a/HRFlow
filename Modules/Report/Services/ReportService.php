<?php

declare(strict_types=1);

namespace Modules\Report\Services;

use Modules\Payroll\Entities\PayrollRun;


final class ReportService
{
    public function __construct(
        private readonly ReportSummaryStoreService $storeService,
        private readonly AttendanceReportService $attendanceReportService,
        private readonly LeaveReportService $leaveReportService,
        private readonly PerformanceReportService $performanceReportService,
        private readonly EmployeeReportService $employeeReportService,
        private readonly PayrollReportService $payrollReportService,
    ) {}

    /**
     * Generate and store a report summary on demand.
     *
     * @return array<string, mixed>
     */
    public function generate(string $reportType, int $month, int $year): array
    {
        $generator = match ($reportType) {
            'attendance' => fn () => $this->attendanceReportService->build($month, $year),
            'leave' => fn () => $this->leaveReportService->build($month, $year),
            'performance' => fn () => $this->performanceReportService->build($year),
            'employees' => fn () => $this->employeeReportService->build($month, $year),
            default => throw new \InvalidArgumentException("Unsupported report type: {$reportType}"),
        };

        $data = $generator();

        $this->storeService->store($reportType, $month, $year, $data);

        return $data;
    }

    public function generatePayroll(PayrollRun $payrollRun): array
{
    $data = $this->payrollReportService->build($payrollRun);

    $this->storeService->store(
        'payroll',
        $payrollRun->month,
        $payrollRun->year,
        $data
    );

    return $data;
}

    /**
     * Read a report summary (from cache or table).
     *
     * @return array<string, mixed>|null
     */
    public function read(string $reportType, int $month, int $year): ?array
    {
        return $this->storeService->read($reportType, $month, $year);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function listByType(string $reportType, ?int $year = null): array
    {
        return $this->storeService->list($reportType, $year);
    }
}
