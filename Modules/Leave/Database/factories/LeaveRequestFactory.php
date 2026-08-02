<?php

namespace Modules\Leave\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Leave\Entities\LeaveRequest;

class LeaveRequestFactory extends Factory
{
    protected $model = LeaveRequest::class;

    public function definition(): array
    {
        return [

            'employee_id' => 1,

            'leave_type_id' => 1,

            'start_date' => now(),

            'end_date' => now()->addDays(2),

            'days_count' => 3,

            'status' => 'pending',

            'manager_approval_status' => 'pending',

            'hr_approval_status' => 'pending',

            'reason' => fake()->sentence(),

        ];
    }
}
 