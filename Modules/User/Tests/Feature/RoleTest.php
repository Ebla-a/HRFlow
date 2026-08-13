<?php

declare(strict_types=1);

namespace Modules\User\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Modules\User\Entities\User;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RoleTest extends TestCase
{
    public function test_create_role(): void
    {
        $admin = User::factory()->create();

        $admin->assignRole('Hr_admin');

        Sanctum::actingAs($admin);

        $roleName = 'TestRole';

        $response = $this->postJson(
            '/api/v1/roles',
            [
                'name' => $roleName,
            ]
        );

        $response
            ->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'Role created successfully',
                'data' => [],
            ]);

        $this->assertDatabaseHas('roles', [
            'name' => $roleName,
            'guard_name' => 'sanctum',
        ]);
    }

    public function test_create_existing_role(): void
    {
        Role::create([
            'name' => 'EmployeeTest',
            'guard_name' => 'sanctum',
        ]);

        $admin = User::factory()->create();

        $admin->assignRole('Hr_admin');

        Sanctum::actingAs($admin);

        $response = $this->postJson(
            '/api/v1/roles',
            [
                'name' => 'EmployeeTest',
            ]
        );

      $response->assertStatus(422)
    ->assertJson([
        'success' => false,
        'message' => 'Validation failed.',
    ])
    ->assertJsonValidationErrors([
        'name',
    ]);
    }

    public function test_delete_role(): void
    {
        $role = Role::create([
            'name' => 'DeleteTestRole',
            'guard_name' => 'sanctum',
        ]);

        $admin = User::factory()->create();

        $admin->assignRole('Hr_admin');

        Sanctum::actingAs($admin);

        $response = $this->deleteJson(
            '/api/v1/roles/' . $role->id
        );

        $response
            ->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'Role deleted successfully',
                'data' => [],
            ]);

        $this->assertDatabaseMissing('roles', [
            'id' => $role->id,
        ]);
    }
}