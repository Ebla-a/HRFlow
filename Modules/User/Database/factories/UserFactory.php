<?php

namespace Modules\User\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Modules\User\Entities\User;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('12345678'),
            'is_active' => true,
            'avatar_url' => fake()->imageUrl(640, 480, 'people', true),
            'email_verified_at' => now(),
        ];
    }
}