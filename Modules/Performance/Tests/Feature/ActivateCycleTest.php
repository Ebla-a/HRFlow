<?php

namespace Modules\Performance\Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Laravel\Sanctum\Sanctum;
use Modules\User\Entities\User;
use Modules\Performance\Entities\PerformanceCycle;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Models\Permission;

class ActivateCycleTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */

    public function test_Active_Cycle_Hr_admin_can_active_the_cycle()
    {
        $permission=Permission::create([
            'name'=>'update.performance.cycle','guard_name' => 'sanctum'
        ]);
        $role=Role::create(['name'=>'Hr_admin','guard_name' => 'sanctum']);
        $role->givePermissionTo($permission);
        $admin = User::factory()->create();
        $admin->assignRole($role);
        Sanctum::actingAs($admin, ['*'], 'sanctum');
        $performance=PerformanceCycle::factory()->create();
        $responce=$this->postJson('/api/v1/performance-cycles/'.$performance->id.'/activate');

        $responce
        ->assertStatus(200)
        ->assertJsonStructure([
            'status',
            'message',
            'data'=>[ 
                    'id',
                    'name',
                    'start_date',
                    'end_date',
                    'status',
            ]
        ])
        ->assertJson([
            'status'=>true,
            'message'=>"Performance cycle activated successfully.",
            'data'=>[
                'id' => $performance->id,
                'name'=>$performance->name,
                'start_date'=>$performance->start_date->toDateTimeString(),
                'end_date'=>$performance->end_date->toDateTimeString(),
                'status'=>'Active',
            ]
        ]);


    }

    public function test_Active_Cycle_Hr_admin_can_colse_the_cycle()
    {
        $permission=Permission::create([
            'name'=>'update.performance.cycle','guard_name' => 'sanctum'
        ]);
        $role=Role::create(['name'=>'Hr_admin','guard_name' => 'sanctum']);
        $role->givePermissionTo($permission);
        $admin = User::factory()->create();
        $admin->assignRole($role);
        Sanctum::actingAs($admin, ['*'], 'sanctum');
        $performance=PerformanceCycle::factory()->create(['status'=>"Active"]);
        $responce=$this->postJson('/api/v1/performance-cycles/'.$performance->id.'/close');

        $responce
        ->assertStatus(200)
        ->assertJsonStructure([
            'status',
            'message',
            'data'=>[ 
                    'id',
                    'name',
                    'start_date',
                    'end_date',
                    'status',
            ]
        ])
        ->assertJson([
            'status'=>true,
            'message'=>"Performance cycle closed successfully.",
            'data'=>[
                'id' => $performance->id,
                'name'=>$performance->name,
                'start_date'=>$performance->start_date->toDateTimeString(),
                'end_date'=>$performance->end_date->toDateTimeString(),
                'status'=>'Closed',
            ]
        ]);


    }

    public function test_Active_Cycle_Hr_admin_can_not_active_the_cycle_if_the_date_pass()
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $permission=Permission::create([
            'name'=>'update.performance.cycle','guard_name' => 'sanctum'
        ]);
        $role=Role::create(['name'=>'Hr_admin','guard_name' => 'sanctum']);
        $role->givePermissionTo($permission);
        $admin = User::factory()->create();
        $admin->assignRole($role);
        Sanctum::actingAs($admin, ['*'], 'sanctum');
        $performance=PerformanceCycle::factory()->create([
            'start_date'=>"2026-09-01 00:00:00",
            'end_date'=>"2026-09-10 00:00:00",
        ]);
        $performance->end_date="2026-01-10 00:00:00";
        $performance->save();

        $responce=$this->postJson('/api/v1/performance-cycles/'.$performance->id.'/activate');

        $responce
        ->assertStatus(422)
        ->assertJsonStructure([
            'status',
            'message',
        ])
        ->assertJson([
            'status'  => false,
            'message' => 'Cannot activate a performance cycle whose end date has passed.',
        ]);

    }


}
