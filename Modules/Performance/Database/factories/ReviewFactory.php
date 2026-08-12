<?php

namespace Modules\Performance\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Employee\Entities\Employee;
use Modules\Performance\Entities\PerformanceCycle;
use Modules\Performance\Entities\PerformanceReview;

class ReviewFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = PerformanceReview::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'employee_id' => Employee::factory(),

            'reviewer_id' => Employee::factory(),

            'performance_cycle_id' => PerformanceCycle::factory()
                ->state([
                    'status' => 'Active',
                ]),

            'status' => $this->faker->randomElement([
                'Draft',
                'Reviewed',
            ]),

            'score' => $this->faker->numberBetween(1, 5),

            'comments' => $this->faker->paragraph(),

            'reviewed_at' => now(),
        ];
    }
}