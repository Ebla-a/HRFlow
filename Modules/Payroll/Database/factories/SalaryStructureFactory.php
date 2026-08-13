<?php

namespace Modules\Payroll\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Payroll\Entities\SalaryStructure;
use Modules\Employee\Entities\Employee;

class SalaryStructureFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SalaryStructure::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'employee_id' => Employee::factory(),
            'basic_salary' => 5000.00,
            'housing_allowance' => 1000.00,
            'transport_allowance' => 500.00,
            'other_allowance' => 0.00,
            'effective_date' => now()->format('Y-m-d'), 
        ];
    }
}