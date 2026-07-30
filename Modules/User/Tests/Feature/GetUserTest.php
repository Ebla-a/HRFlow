<?php

namespace Modules\User\Tests\Feature;


use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Modules\User\Entities\User;
use Spatie\Permission\Models\Role; 
use Tests\TestCase;

class GetUserTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_Get_one_user()
    {
        $role=Role::create(['name' => 'Hr_admin']);
        $admin = User::factory()->create();
        $admin->assignRole('Hr_admin');
        $user = User::factory()->create();

        Sanctum::actingAs($admin);
        $response = $this->getJson('/api/v1/user/' . $user->id);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'id',
                    'email',
                    'is_active',
                    'avatar_url',
                    'created_at',
                    'updated_at',
                ],
            ])
            ->assertJson([
                'status' => true,
                'data'   => [
                    'id'    => $user->id,
                    'email' => $user->email,
                ],
            ]);
    }
}
