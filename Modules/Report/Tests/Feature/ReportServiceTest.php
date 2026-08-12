<?php

namespace Modules\Report\Tests\Feature;

use InvalidArgumentException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Report\Services\AttendanceReportService;
use Modules\Report\Services\EmployeeReportService;
use Modules\Report\Services\LeaveReportService;
use Modules\Report\Services\PayrollReportService;
use Modules\Report\Services\PerformanceReportService;
use Modules\Report\Services\ReportService;
use Modules\Report\Services\ReportSummaryStoreService;
use Tests\TestCase;

class ReportServiceTest extends TestCase
{
    use RefreshDatabase;

    private function makeService(ReportSummaryStoreService $storeService): ReportService
    {
        return new ReportService(
            $storeService,
            app(AttendanceReportService::class),
            app(LeaveReportService::class),
            app(PerformanceReportService::class),
            app(EmployeeReportService::class),
            app(PayrollReportService::class),
        );
    }

    public function test_service_can_be_resolved_from_container(): void
    {
        $this->assertInstanceOf(ReportService::class, app(ReportService::class));
    }

public function test_generate_throws_for_unsupported_report_type(): void
    {
        $service = $this->makeService(app(ReportSummaryStoreService::class));

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unsupported report type: unknown');

        $service->generate('unknown', 8, 2026);
    }

    public function test_read_returns_null_when_no_summary_exists(): void
    {
        $storeService = app(ReportSummaryStoreService::class);

        $service = $this->makeService($storeService);

        $this->assertNull($service->read('attendance', 8, 2026));
    }

    public function test_list_delegates_to_store_service(): void
    {
        $storeService = app(ReportSummaryStoreService::class);

        $service = $this->makeService($storeService);

        $this->assertIsArray($service->listByType('attendance', 2026));
    }
}
