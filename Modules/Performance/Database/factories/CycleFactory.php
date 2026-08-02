<?php
namespace Modules\Performance\Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Modules\Performance\Entities\performance_cycle;


class CycleFactory extends Factory
{

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \Modules\Performance\Entities\performance_cycle::class;


    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $startDate = Carbon::instance(
            $this->faker->dateTimeBetween('+1 day', '+1 month')
        );

        $endDate = (clone $startDate)->addDays($this->faker->numberBetween(3, 14));

        return [
            'name'       => $this->faker->word(),
            'start_date' => $startDate->toDateString(), 
            'end_date'   => $endDate->toDateString(), 
            'status'     => $this->faker->randomElement(['Active', 'Closed','Draft']),
        ];
    }

}