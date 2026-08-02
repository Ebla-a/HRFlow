<?php

namespace Modules\User\Tests\Featur;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Modules\User\Entities\User;
use Spatie\Permission\Models\Role; 
use Tests\TestCase;

class RoleTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    use RefreshDatabase;

    public function test_create_role()
    {
        Role::create(['name'=>"Hr_admin"]);
        $admin=User::factory()->create();
        $admin->assignRole('Hr_admin');
        $user = User::factory()->create();
        Sanctum::actingAs($admin);

        $response=$this->postJson('/api/v1/Hr/createRole',[
            'role'=>"Employee"
        ]);

        $response->assertStatus(200)
        ->assertJsonStructure([
                'status' ,
                'message',
                'data' ,
            ])
        ->assertJson([
            'status'=>true,
            'message'=>'Role created successfully',
            'data'=>[]
        ]);

    }

    public function test_create_existing_role()
    {
        Role::create(['name'=>"Hr_admin"]);
        Role::create(['name'=>"Employee"]);
        $admin=User::factory()->create();
        $admin->assignRole('Hr_admin');
        Sanctum::actingAs($admin);

        $response=$this->postJson('/api/v1/Hr/createRole',[
            'role'=>"Employee"
        ]);

        $response->assertStatus(422)
        ->assertJsonStructure([
                'status'  ,
                'message' ,
                'errors'  ,
            ])
        ->assertJson([
            'status'  => false,
            'message' => 'Validation error',
            'errors'  => [
                    'role' => ['A role with this name already exists.'],
                ],
        ]);

    }

    public function test_delete__role()
    {
        Role::create(['name'=>"Hr_admin"]);
        $role=Role::create(['name'=>"Employee"]);
        $admin=User::factory()->create();
        $admin->assignRole('Hr_admin');
        Sanctum::actingAs($admin);

        $response=$this->postJson('/api/v1/Hr/deleteRole/'.$role->id);

        $response->assertStatus(200)
        ->assertJsonStructure([
                'status'  ,
                'message' ,
                'data'  ,
            ])
        ->assertJson([
            'status'  => true,
            'message' => 'Role deleted successfully',
            'data'  => [],
        ]);
        
        $this->assertDatabaseMissing('roles', [
            'id' => $role->id,
        ]);

    }


}
