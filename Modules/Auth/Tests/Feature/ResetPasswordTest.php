<?php

namespace Modules\Auth\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class ResetPasswordTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_reset_password_with_valid_token()
    {
        $user = User::factory()->create(['email' => 'user@example.com']);
        $token = Str::random(60);

        DB::table('password_reset_tokens')->insert([
            'email' => 'user@example.com',
            'token' => Hash::make($token),
            'created_at' => now(),
        ]);

        $response = $this->postJson('/api/v1/auth/reset-password', [
            'email' => 'user@example.com',
            'token' => $token,
            'password' => 'newsecret123',
            'password_confirmation' => 'newsecret123',
        ]);

        $response->assertStatus(200);
        $this->assertTrue(Hash::check('newsecret123', $user->fresh()->password));
    }

    public function test_reset_password_fails_with_invalid_token()
   {
    $user = User::factory()->create();

    $response = $this->postJson('/api/v1/auth/reset-password', [
        'email' => $user->email,
        'token' => 'invalid-token',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertStatus(400);
   }

}
 