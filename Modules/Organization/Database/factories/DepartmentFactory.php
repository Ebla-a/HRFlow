<?php

namespace Modules\Organization\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Organization\Entities\Department;

class DepartmentFactory extends Factory
{
    protected $model = Department::class;

    public function definition(): array
    {
        return [
            'name'        => fake()->unique()->jobTitle() . ' Department',
            'code'        => 'DEP-' . strtoupper(fake()->unique()->lexify('???')),
            'parent_id'   => null,
            'manager_id'  => null,
            'is_active'   => true,
        ];
    }


    public function withParent(int $parentId): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => $parentId,
        ]);
    }
}
