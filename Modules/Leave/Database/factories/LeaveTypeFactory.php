<?php

namespace Modules\Leave\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Leave\Entities\LeaveType;

class LeaveTypeFactory extends Factory
{
    protected $model = LeaveType::class;

    public function definition(): array
    {
        return [

            'name' => fake()->unique()->word(),

            'annual_days' => 21,

            'is_paid' => true,

            'requires_attachment' => false,

        ];
    }
}
 