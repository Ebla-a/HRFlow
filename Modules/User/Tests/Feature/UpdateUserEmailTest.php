<?php

namespace Modules\User\Tests\Feature;


use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Modules\User\Entities\User;
use Spatie\Permission\Models\Role; 
use Tests\TestCase;

class UpdateUserEmailTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_authenticated_admin_can_update_user_email()
    {
        $role=Role::create(['name' => 'Hr_admin']);
        $admin = User::factory()->create();
        $admin->assignRole('Hr_admin');
        $user = User::factory()->create([
            'email'=> 'old_email@example.com',
            'email_verified_at' => now(),
        ]);
        $newEmail = 'updated_email@example.com';

        Sanctum::actingAs($admin);
        $response = $this->postJson(route('updateEmail'), [
            'id'    => $user->id,
            'email' => $newEmail,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status'  => true,
                'message' => 'Email updated successfully',
                'data'    => [
                    'id'    => $user->id,
                    'email' => $newEmail,
                ],
            ])
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
            ]);

        // 4. Assert Database Changes
        $this->assertDatabaseHas('users', [
            'id'                => $user->id,
            'email'             => $newEmail,
            'email_verified_at' => null,
        ])
        ;
    }
}
