<?php

namespace Modules\Auth\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ResetPasswordEventTest extends TestCase
{
    use RefreshDatabase;


    public function test_reset_password_logs_change_and_logout_old_devices()
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => bcrypt('old-password'),
        ]);


        // Create reset token
        $token = 'reset-token';


        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => bcrypt($token),
            'created_at' => now(),
        ]);


        // Create old device token
        $user->createToken('old-device');


        $response = $this->postJson('/api/v1/auth/reset-password', [
            'email' => $user->email,
            'token' => $token,
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);


        $response->assertStatus(200);


        // old tokens deleted
        $this->assertDatabaseCount(
            'personal_access_tokens',
            0
        );


        // password change logged
        $this->assertDatabaseHas(
            'password_change_logs',
            [
                'user_id' => $user->id,
            ]
        );
    }
}
 