<?php

namespace Modules\Report\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Report\Services\ReportSummaryStoreService;
use Tests\TestCase;

class ReportSummaryTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_and_read_report_summary(): void
    {
        $service = app(ReportSummaryStoreService::class);

        $data = [
            'period' => '2026-08',
            'month' => 8,
            'year' => 2026,
            'total_records' => 10,
        ];

        $service->store('attendance', 8, 2026, $data);

        $this->assertDatabaseHas('report_summaries', [
            'report_type' => 'attendance',
            'month' => 8,
            'year' => 2026,
        ]);

        $read = $service->read('attendance', 8, 2026);

        $this->assertNotNull($read);
        $this->assertSame('2026-08', $read['period']);
        $this->assertSame(10, $read['total_records']);
    }

    public function test_store_updates_existing_report_summary(): void
    {
        $service = app(ReportSummaryStoreService::class);

        $service->store('leave', 8, 2026, ['total_records' => 5]);
        $service->store('leave', 8, 2026, ['total_records' => 8]);

        $this->assertDatabaseCount('report_summaries', 1);

        $read = $service->read('leave', 8, 2026);

        $this->assertSame(8, $read['total_records']);
    }

    public function test_read_missing_report_returns_null(): void
    {
        $service = app(ReportSummaryStoreService::class);

        $this->assertNull($service->read('attendance', 1, 1999));
    }

    public function test_list_reports_by_type_and_year(): void
    {
        $service = app(ReportSummaryStoreService::class);

        $service->store('attendance', 7, 2026, ['period' => '2026-7']);
        $service->store('attendance', 8, 2026, ['period' => '2026-8']);
        $service->store('leave', 8, 2026, ['period' => '2026-8']);

        $attendance = $service->list('attendance', 2026);
        $this->assertCount(2, $attendance);

        $leave = $service->list('leave', 2026);
        $this->assertCount(1, $leave);

        $allAttendance = $service->list('attendance');
        $this->assertCount(2, $allAttendance);
    }
}
