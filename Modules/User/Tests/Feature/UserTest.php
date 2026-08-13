<?php

declare(strict_types=1);

namespace Modules\User\Tests\Feature;

use Laravel\Sanctum\Sanctum;
use Modules\User\Entities\User;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function test_it_can_get_all_users_paginated_with_correct_structure(): void
    {
        $admin = User::factory()->create();

        $admin->assignRole('Hr_admin');

        User::factory()
            ->count(15)
            ->create();

        Sanctum::actingAs($admin);

        $response = $this->getJson(
            route('users.index')
        );

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'data',
                    'links',
                    'meta',
                ],
            ]);
    }

    public function test_unauthenticated_user_cannot_get_users(): void
    {
        $response = $this->getJson(
            route('users.index')
        );

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);
    }

    public function test_authenticated_user_without_hr_admin_role_cannot_get_users(): void
    {
        $employee = User::factory()->create();

        $employee->assignRole('Employee');

        Sanctum::actingAs($employee);

        $response = $this->getJson(
            route('users.index')
        );

        $response->assertStatus(403);
    }
}