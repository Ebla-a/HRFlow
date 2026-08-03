<?php

namespace Modules\Auth\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UpdatePasswordTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_update_password_with_valid_current_password()
    {
        $user = User::factory()->create([
            'password' => bcrypt('oldpassword123'),
        ]);

        Sanctum::actingAs($user);

        $response = $this->putJson('/api/v1/auth/password', [
            'current_password' => 'oldpassword123',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertStatus(200);
        $this->assertTrue(Hash::check('newpassword123', $user->fresh()->password));
    }

    public function test_user_cannot_update_password_with_invalid_current_password()
    {
        $user = User::factory()->create([
            'password' => bcrypt('oldpassword123'),
        ]);

        Sanctum::actingAs($user);

        $response = $this->putJson('/api/v1/auth/password', [
            'current_password' => 'wrongoldpassword',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertStatus(422);
    }

    public function test_guest_cannot_update_password()
   {
    $response = $this->putJson('/api/v1/auth/password', []);

    $response->assertStatus(401);
   }
}
 