<?php

declare(strict_types=1);

namespace Modules\Report\Services;

use Modules\Leave\Entities\LeaveRequest;
use Modules\Leave\Enums\LeaveRequestStatusEnum;

final class LeaveReportService
{
    /**
     * Build a leave report summary for a given month/year.
     *
     * @return array<string, mixed>
     */
    public function build(int $month, int $year): array
    {
        $requests = LeaveRequest::query()
            ->with('leaveType')
            ->whereYear('start_date', $year)
            ->whereMonth('start_date', $month)
            ->get();

        $approved = $requests->where('status', LeaveRequestStatusEnum::APPROVED);
        $pending = $requests->where('status', LeaveRequestStatusEnum::PENDING);
        $rejected = $requests->where('status', LeaveRequestStatusEnum::REJECTED);

        return [
            'period' => "{$year}-{$month}",
            'month' => $month,
            'year' => $year,
            'total_requests' => $requests->count(),
            'total_employees_covered' => $requests->pluck('employee_id')->unique()->count(),
            'status_breakdown' => [
                'approved' => $approved->count(),
                'pending' => $pending->count(),
                'rejected' => $rejected->count(),
            ],
            'totals' => [
                'approved_days' => $approved->sum('days_count'),
                'pending_days' => $pending->sum('days_count'),
            ],
            'generated_at' => now()->toDateTimeString(),
        ];
    }
}
