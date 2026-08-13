<?php

declare(strict_types=1);

namespace Modules\User\Tests\Feature;

use Laravel\Sanctum\Sanctum;
use Modules\User\Entities\User;
use Tests\TestCase;

class ActiveUserAccountTest extends TestCase
{
    public function test_activate_user_account(): void
    {
        $admin = User::factory()->create();

        $admin->assignRole('Hr_admin');

        $user = User::factory()->create([
            'is_active' => false,
        ]);

        Sanctum::actingAs($admin);

        $response = $this->postJson(
            route('users.activate', [
                'user' => $user->id,
            ])
        );

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'User activated successfully.',
                'data' => [],
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'is_active' => true,
        ]);
    }

    public function test_deactivate_user_account(): void
    {
        $admin = User::factory()->create();

        $admin->assignRole('Hr_admin');

        $user = User::factory()->create([
            'is_active' => true,
        ]);

        Sanctum::actingAs($admin);

        $response = $this->postJson(
            route('users.deactivate', [
                'user' => $user->id,
            ])
        );

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'User deactivated successfully.',
                'data' => [],
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'is_active' => false,
        ]);
    }
}