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

class CreateRevieTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_Create_review()
    {
        $permission1=Permission::create([
            'name'=>'view.reviews.department','guard_name' => 'sanctum'
        ]);
        $permission2=Permission::create([
            'name'=>'view.reviews.all','guard_name' => 'sanctum'
        ]);
        $role=Role::create(['name'=>'Hr_admin','guard_name' => 'sanctum']);
        $role->givePermissionTo($permission1);
        $role->givePermissionTo($permission2);
        $admin = User::factory()->create();
        $admin->assignRole($role);
        Sanctum::actingAs($admin, ['*'], 'sanctum');
        $performance=PerformanceCycle::factory()->create();
        
        $responce=$this->getJson('/api/v1/performance-reviews');

    }

}
