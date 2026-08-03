<?php

namespace Modules\Performance\Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Laravel\Sanctum\Sanctum;
use Modules\Performance\Entities\performance_cycle;
use Modules\User\Entities\User;

class ShowCyclesTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_Show_Cycles_for_admin()
    {

        $role=Role::create(['name' => 'Hr_admin','guard_name'=>'sanctum']);
        $admin = User::factory()->create();
        $admin->assignRole('Hr_admin');
        Sanctum::actingAs($admin);
        $data=performance_cycle::factory()->create();

        $response=$this->getJson('/api/v1/performance-cycles');
        $response->assertStatus(200);



    }
}
