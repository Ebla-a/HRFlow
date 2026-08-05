<?php

namespace Modules\Performance\Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Laravel\Sanctum\Sanctum;
use Modules\Performance\Entities\performance_cycle;
use Modules\User\Entities\User;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Models\Permission;

class ShowReviewsTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */

    public function test_Show_Reviews()
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $this->app->make(PermissionRegistrar::class)->forgetCachedPermissions();
        $permission1=Permission::create([
            'name'=>'view.reviews.department','guard_name' => 'sanctum'
        ]);
        $permission2=Permission::create([
            'name'=>'view.reviews.all','guard_name' => 'sanctum'
        ]);

        $role=Role::create(['name'=>'Manager','guard_name' => 'sanctum']);
        $role->givePermissionTo($permission2);
        $admin = User::factory()->create();
        $admin->assignRole($role);
        $admin->unsetRelation('roles');
        Sanctum::actingAs($admin, ['*'], 'sanctum');

        $response=$this->getJson('/api/v1/performance-reviews')
        ->assertStatus(200)
        ->assertJsonStructure([
            'status',
            'message',
            'data' => [
                'data' => [
                    '*' => [
                    'id',
                    'cycle_id' ,
                    'employee_id',
                    'manager_id',
                    'status',
                    'score',
                    'comments',
                    'reviewed_at' ,
                    'status_cycle',
                    'cycle_name',
                    'employee_name',
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
            'message'=>"Performance reviews retrieved successfully.",
        ])
        ;
        
        
    }


}
