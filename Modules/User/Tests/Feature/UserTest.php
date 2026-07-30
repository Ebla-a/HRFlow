<?php

namespace Modules\User\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Modules\User\Entities\User;
use Spatie\Permission\Models\Role; 
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_get_all_users_paginated_with_correct_structure()
    {
        
        $role=Role::create(['name' => 'Hr_admin']);
        $admin = User::factory()->create();
        $admin->assignRole('Hr_admin');
        User::factory()->count(15)->create();

        Sanctum::actingAs($admin);
        $response = $this->getJson('/api/v1/users');
        

    $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'email',
                        'is_active',
                        'avatar_url' ,
                        'created_at',
                        'updated_at',
                    ],
                ],
                'links' => [
                    'first',
                    'last',
                    'prev',
                    'next',
                ],
                'meta' => [
                    'current_page',
                    'from',
                    'last_page',
                    'per_page',
                    'to',
                    'total',
                ],
            ]);
    }

    public function test_unauthenticated_user_cannot_get_users()
{

    $response = $this->getJson('/api/v1/users');
    $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);
}

public function test_authenticated_user_without_hr_admin_role_cannot_get_users()
{
    
    $employeeRole = $role=Role::create(['name' => 'Employee']);
    $employee = User::factory()->create();
    $employee->assignRole($employeeRole);
    Sanctum::actingAs($employee);
    $response = $this->getJson('/api/v1/users');
    $response->assertStatus(403);
}


}