<?php

namespace Modules\Attendance\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Employee\Entities\Employee;
use Modules\Attendance\Entities\Attendance;
use Modules\Leave\Enums\LeaveRequestStatusEnum;

class MarkAbsentEmployeesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $today = now()->toDateString();

        Employee::where('status', 'active')
            ->chunk(500, function ($employees) use ($today) {
                foreach ($employees as $employee) {
                    $hasActiveLeave = $employee->leaveRequests()
                        ->where('status', LeaveRequestStatusEnum::APPROVED->value)
                        ->whereDate('start_date', '<=', $today)
                        ->whereDate('end_date', '>=', $today)
                        ->exists();

                    $status = $hasActiveLeave ? 'on_leave' : 'absent';

                    Attendance::firstOrCreate(
                        [
                            'employee_id' => $employee->id,
                            'attendance_date' => $today,
                        ],
                        [
                            'status' => $status,
                            'late_minutes' => 0,
                            'worked_minutes' => 0,
                            'overtime_minutes' => 0,
                        ]
                    );
                }
            });
    }
}