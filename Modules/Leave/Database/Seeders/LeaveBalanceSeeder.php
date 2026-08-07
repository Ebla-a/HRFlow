<?php

namespace Modules\Leave\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Leave\Entities\LeaveBalance;
use Modules\Leave\Entities\LeaveType;
use Modules\Employee\Entities\Employee;

class LeaveBalanceSeeder extends Seeder
{
    public function run(): void
    {
        $employees = Employee::all();
        $leaveTypes = LeaveType::all();

        foreach ($employees as $employee) {
            foreach ($leaveTypes as $type) {
                LeaveBalance::firstOrCreate(
                    [
                        'employee_id'   => $employee->id,
                        'leave_type_id' => $type->id,
                        'year'          => now()->year,
                    ],
                    [
                        'total_days'     => $type->annual_days ?? 21,
                        'used_days'      => 0,
                        'remaining_days' => $type->annual_days ?? 21,
                    ]
                );
            }
        }
    }
}