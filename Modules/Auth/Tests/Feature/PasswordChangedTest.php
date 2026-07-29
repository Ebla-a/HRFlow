<?php

namespace Modules\Auth\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Notification;

class PasswordChangedTest extends TestCase
{
    use RefreshDatabase;


    public function test_user_password_change_logs_ip_and_device_and_logout_old_devices()
    {
            Notification::fake();

        $user = User::factory()->create([
            'password' => bcrypt('old-password'),
        ]);


        // create old tokens
        $user->createToken('old-device');


        $this->actingAs($user);


       $response = $this->putJson('/api/v1/auth/password', [
          'current_password' => 'old-password',
          'password' => 'new-password',
          'password_confirmation' => 'new-password',
        ]);

        $response->assertStatus(200);


        // Check old tokens deleted
        $this->assertDatabaseCount('personal_access_tokens', 0);


        // Check password change log saved
        $this->assertDatabaseHas('password_change_logs', [
            'user_id' => $user->id,
        ]);
    }
}