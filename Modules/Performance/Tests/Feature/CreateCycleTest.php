<?php

namespace Modules\Performance\Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\PermissionRegistrar;
use Modules\User\Entities\User;
use Spatie\Permission\Models\Permission;

class CreateCycleTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_Create_Cycle_with_role_HR_admin()
    {
        $this->withoutExceptionHandling();
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permission=Permission::create(['name'=>'create.performance.cycle','guard_name' => 'sanctum']);
        $role = Role::create(['name' => 'Hr_admin', 'guard_name' => 'sanctum']);
        $role->givePermissionTo($permission);
        $admin = User::factory()->create();
        $admin->assignRole($role);
        Sanctum::actingAs($admin, ['*'], 'sanctum');

        $responce=$this->postJson('/api/v1/performance-cycles',
        [
            'name'=>"test",
            'start_date'=>"2026-09-01 00:00:00",
            'end_date'=>"2026-09-10 00:00:00",
        ]);
        
        $responce
        ->assertStatus(201)
        ->assertJsonStructure([
            'status',
            'message',
            'data'=>[
                'id',
                'name',
                'start_date',
                'end_date',
                'status',
            ],
        ])
        ->assertjson([
        'status'=>true,
        'message'=>'Performance cycle created successfully.',
        'data'=>[
                'id'=>1,
                'name'=>'test',
                'start_date'=>"2026-09-01 00:00:00",
                'end_date'=>"2026-09-10 00:00:00",
                'status'=>'Draft',
            ]
        ]);

        $this->assertDatabaseHas('performance_cycles', [
            'id'=>1,
            'name'=>'test',
            'start_date'=>"2026-09-01 00:00:00",
            'end_date'=>"2026-09-10 00:00:00",
            'status'=>'Draft',
        ]);
    }

    public function test_Create_Cycle_with_role_HR_admin_with_bad_word_and_passed_start_day()
    {
        $this->withoutExceptionHandling();
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permission=Permission::create(['name'=>'create.performance.cycle','guard_name' => 'sanctum']);
        $role = Role::create(['name' => 'Hr_admin', 'guard_name' => 'sanctum']);
        $role->givePermissionTo($permission);
        $admin = User::factory()->create();
        $admin->assignRole($role);
        Sanctum::actingAs($admin, ['*'], 'sanctum');

        $responce=$this->postJson('/api/v1/performance-cycles',
        [
            'name'=>"badword1",
            'start_date'=>"2026-05-01 00:00:00",
            'end_date'=>"2026-03-10 00:00:00",
        ]);
        
        $responce
        ->assertStatus(422)
        ->assertJsonStructure([
            'status',
            'message',
            'errors'
        ])
        ->assertJson([
            'status'  => false,
            'message' => 'Validation errors',
            'errors'  =>[
                'name'=>[
                    "The name contains inappropriate words."
                    ],
                'start_date'=>[
                    'The start date must be a date after today.'
                ],
                'end_date'=>[
                    'The end date must be at least 3 days after the start date.'
                ]
            ]
        ]);
    }
}
