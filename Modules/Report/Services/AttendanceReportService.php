<?php

declare(strict_types=1);

namespace Modules\Report\Services;

use Modules\Attendance\Entities\Attendance;

final class AttendanceReportService
{
    /**
     * Build an attendance report summary for a given month/year.
     *
     * @return array<string, mixed>
     */
    public function build(int $month, int $year): array
    {
        $attendances = Attendance::query()
            ->whereYear('attendance_date', $year)
            ->whereMonth('attendance_date', $month)
            ->get();

        $present = $attendances->where('status', 'present');
        $absent = $attendances->where('status', 'absent');
        $late = $attendances->where('status', 'late');
        $onLeave = $attendances->where('status', 'on_leave');
        $holiday = $attendances->where('status', 'holiday');

        return [
            'period' => "{$year}-{$month}",
            'month' => $month,
            'year' => $year,
            'total_records' => $attendances->count(),
            'total_employees_covered' => $attendances->pluck('employee_id')->unique()->count(),
            'status_breakdown' => [
                'present' => $present->count(),
                'absent' => $absent->count(),
                'late' => $late->count(),
                'on_leave' => $onLeave->count(),
                'holiday' => $holiday->count(),
            ],
            'totals' => [
                'worked_minutes' => $attendances->sum('worked_minutes'),
                'late_minutes' => $attendances->sum('late_minutes'),
                'overtime_minutes' => $attendances->sum('overtime_minutes'),
            ],
            'generated_at' => now()->toDateTimeString(),
        ];
    }
}
