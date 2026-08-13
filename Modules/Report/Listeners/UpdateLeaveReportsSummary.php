<?php

declare(strict_types=1);

namespace Modules\Report\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Modules\Leave\Events\LeaveRequestApproved;
use Modules\Report\Services\LeaveReportService;
use Modules\Report\Services\ReportSummaryStoreService;

class UpdateLeaveReportsSummary implements ShouldQueue
{
    public $tries = 3;

    public function __construct(
        private readonly LeaveReportService $leaveReportService,
        private readonly ReportSummaryStoreService $storeService
    ) {}

    /**
     * Handle the "leave request approved" event.
     *
     * @param LeaveRequestApproved $event
     * @return void
     */
    public function handle(LeaveRequestApproved $event): void
    {
        $leaveRequest = $event->leaveRequest;

        $month = (int) $leaveRequest->start_date->format('n');
        $year = (int) $leaveRequest->start_date->format('Y');

        $data = $this->leaveReportService->build($month, $year);

        $this->storeService->store('leave', $month, $year, $data);

        Log::info("Leave report summary generated for {$year}-{$month}");
    }
}
