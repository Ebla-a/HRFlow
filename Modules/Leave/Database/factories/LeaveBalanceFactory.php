<?php

namespace Modules\Leave\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Leave\Entities\LeaveBalance;

class LeaveBalanceFactory extends Factory
{
    protected $model = LeaveBalance::class;

    public function definition(): array
    {
        return [

            'employee_id' => 1,

            'leave_type_id' => 1,

            'year' => now()->year,

            'accrual_days' => 21,

            'used_days' => 0,

            'remaining_days' => 21,

        ];
    }
}
 