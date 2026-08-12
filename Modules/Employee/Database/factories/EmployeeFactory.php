<?php

namespace Modules\Employee\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Employee\Entities\Employee;
use Modules\Organization\Entities\JobTitle;
use Modules\User\Entities\User;

class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    public function definition(): array
    {
        return [
            'employee_number' => 'EMP-' . $this->faker->unique()->numberBetween(1000, 9999),
            'user_id' => User::factory(),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'job_title_id'    => JobTitle::factory(),
           'national_id' => $this->faker->unique()->numerify('##########'),
            'birth_date' => $this->faker->date(),
            'gender' => $this->faker->randomElement(['male', 'female']),
            'employment_type' => 'full_time', 
            'status'          => 'active', 
            'hire_date' => '2026',
        ];
    }
}