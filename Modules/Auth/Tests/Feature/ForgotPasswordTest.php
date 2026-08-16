<?php

namespace Modules\Auth\Tests\Feature;

use Modules\User\Entities\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ForgotPasswordTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_request_password_reset_link()
    {
        $user = User::factory()->create(['email' => 'user@example.com']);

        $response = $this->postJson('/api/v1/auth/forgot-password', [
            'email' => 'user@example.com',
        ]);

        $response->assertStatus(200)
        ->assertJsonStructure([
             'status',
             'message',
             'data',
         ]);

        $this->assertDatabaseHas('password_reset_tokens', [
            'email' => 'user@example.com',
        ]);
    }
}
