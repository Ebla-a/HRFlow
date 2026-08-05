<?php
namespace Modules\Performance\Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Modules\Employee\Entities\Employee;
use Modules\Performance\Entities\performanceCycle;



class ReviewFactory extends Factory
{

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \Modules\Performance\Entities\PerformanceReview::class;


    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'employee_id' => Employee::factory(),
            'reviewer_id' => Employee::factory(),
            'cycle_id'    => PerformanceCycle::factory()->state(['status' => 'Active']),
            'status'      => $this->faker->randomElement(['Draft', 'Reviewed']),
            'score'       => $this->faker->numberBetween(1, 5),
            'comments'    => $this->faker->paragraph(),
        ];

    }
}

