<?php

namespace Modules\Organization\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Organization\Entities\Department;
use Modules\Organization\Entities\JobTitle;


class JobTitleFactory extends Factory
{
    protected $model = JobTitle::class;

    public function definition(): array
    {
        return [
             'title' => $this->faker->unique()->jobTitle(),
            'grade'         => fake()->randomElement(['junior', 'med', 'senior', 'lead','manager']),
            'department_id' => Department::factory(),
            'description'   => fake()->sentence(10),
            'is_active'     => true,
        ];
    }
}
