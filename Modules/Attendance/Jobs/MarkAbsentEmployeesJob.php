<?php

namespace Modules\Attendance\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Modules\Employee\Entities\Employee;
use Modules\Attendance\Entities\Attendance;

/**
 * Summary of MarkAbsentEmployeesJob
 */
class MarkAbsentEmployeesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Summary of handle
     * @return void
     */
    public function handle()
    {
        $today = now()->toDateString();


        Employee::where('status', 'active')
            ->chunk(500, function ($employees) use ($today) {


                foreach ($employees as $employee) {


    Attendance::firstOrCreate(
    [
        'employee_id' => $employee->id,
        'attendance_date' => $today,
    ],
    [
        'status' => 'absent',
        'late_minutes' => 0,
        'worked_minutes' => 0,
        'overtime_minutes' => 0,
    ]
);
   }
     });
    }
}