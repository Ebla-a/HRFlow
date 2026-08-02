<?php

namespace Modules\User\Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\User\Entities\User;
use Spatie\Permission\Models\Role;
use Laravel\Sanctum\Sanctum;

class ActiveUserAccountTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_activate_user_accoutn()
    {
        $role=Role::create(['name' => 'Hr_admin']);
        $admin = User::factory()->create();
        $admin->assignRole('Hr_admin');
        $user = User::factory()->create();

        Sanctum::actingAs($admin);
        $response = $this->postJson('/api/v1/user/activeUserAccount/' . $user->id);

        $response->assertStatus(200)
        ->assertJsonStructure([
                'status' ,
                'message',
                'data' ,
            ])
            ->assertJson([
                'status' => true,
                'message' => "User activated successfully",
                'data' => [],
            ]);

        $this->assertDatabaseHas('users', [
            'id'                => $user->id,
            'is_active'         => true,
            
        ])
        ;


    }

    public function test_disactivate_user_accoutn()
    {
        $role=Role::create(['name' => 'Hr_admin']);
        $admin = User::factory()->create();
        $admin->assignRole('Hr_admin');
        $user = User::factory()->create();

        Sanctum::actingAs($admin);
        $response = $this->postJson('/api/v1/user/disActiveUserAccount/' . $user->id);

        $response->assertStatus(200)
        ->assertJsonStructure([
                'status' ,
                'message',
                'data' ,
            ])
            ->assertJson([
                'status' => true,
                'message' => "User disactivated successfully",
                'data' => [],
            ]);

        $this->assertDatabaseHas('users', [
            'id'                => $user->id,
            'is_active'         => false,
            
        ])
        ;


    }

    public function test()
    {
        $role=Role::create(['name' => 'Hr_admin']);
        $admin = User::factory()->create();
        $admin->assignRole('Hr_admin');
        $user = User::factory()->create([
            'is_active'=>false
        ]);

        Sanctum::actingAs($admin);
        $response = $this->postJson('/api/v1/user/disActiveUserAccount/' . $user->id);
        $response->assertStatus(200)
        ->assertJsonStructure([
                'status' ,
                'message',
                'data' ,
            ])
            ->assertJson([
                'status' => true,
                'message' => "User disactivated successfully",
                'data' => [],
            ]);

        $this->assertDatabaseHas('users', [
            'id'                => $user->id,
            'is_active'         => false,
            
        ])
        ;


    }

}
