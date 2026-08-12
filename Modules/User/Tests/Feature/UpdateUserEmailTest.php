<?php

declare(strict_types=1);

namespace Modules\User\Tests\Feature;

use Laravel\Sanctum\Sanctum;
use Modules\User\Entities\User;
use Tests\TestCase;

class UpdateUserEmailTest extends TestCase
{
    public function test_authenticated_admin_can_update_user_email(): void
    {
        $admin = User::factory()->create();

        $admin->assignRole('Hr_admin');

        $user = User::factory()->create([
            'email' => 'old_email@example.com',
            'email_verified_at' => now(),
        ]);

        Sanctum::actingAs($admin);

        $newEmail = 'updated_email@example.com';

        $response = $this->putJson(
            "/api/v1/users/{$user->id}/email",
            [
                'email' => $newEmail,
            ]
        );

        $response->assertStatus(200);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email' => $newEmail,
            'email_verified_at' => null,
        ]);
    }
}