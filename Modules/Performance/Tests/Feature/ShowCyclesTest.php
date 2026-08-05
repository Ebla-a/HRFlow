<?php

namespace Modules\Performance\Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\PermissionRegistrar;
use Modules\Performance\Entities\PerformanceCycle;
use Modules\User\Entities\User;

class ShowCyclesTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_Show_Cycles_for_authenticated_users_with_role()
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $role = Role::create(['name' => 'Hr_admin', 'guard_name' => 'sanctum']);
        $admin = User::factory()->create();
        $admin->assignRole($role);
        Sanctum::actingAs($admin, ['*'], 'sanctum');
        PerformanceCycle::factory()->count(5)->create();

        $response=$this->getJson('/api/v1/performance-cycles');
        $response
        ->assertStatus(200)
        ->assertJsonStructure([
            'status',
            'message',
            'data' => [
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'start_date',
                        'end_date',
                        'status',
                    ]
                ],
                'meta' => [
                    'current_page',
                    'last_page',
                    'per_page',
                    'total'
                ]
            ]
        ])
        ->assertJson([
            'status'=>true,
            'message' => 'Performance cycles retrieved successfully.',
            'data'=>[]
        ]);

    }

    public function test_Show_Cycles_for_non_authenticated_users()
    {

        $user = User::factory()->create();
        PerformanceCycle::factory()->count(5)->create();

        $response=$this->getJson('/api/v1/performance-cycles');
        $response
        ->assertStatus(401)
        ->assertJsonStructure([
            'message'
        ])
        ->assertJson([
            'message' => 'Unauthenticated.'
        ]);
    }

    public function test_Show_Cycles_for_authenticated_users_without_role()
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*'], 'sanctum');
        PerformanceCycle::factory()->count(5)->create();

        $response=$this->getJson('/api/v1/performance-cycles');
        $response
        ->assertStatus(403)
        ->assertJsonStructure([
            "message"
        ])
        ->assertJson([
            'message' => 'This action is unauthorized.'
        ]); 
    }
}
