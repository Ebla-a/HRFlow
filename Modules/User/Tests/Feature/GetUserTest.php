<?php

declare(strict_types=1);

namespace Modules\User\Tests\Feature;

use Laravel\Sanctum\Sanctum;
use Modules\User\Entities\User;
use Tests\TestCase;

class GetUserTest extends TestCase
{
    public function test_get_one_user(): void
    {
        $admin = User::factory()->create();

        $admin->assignRole('Hr_admin');

        $user = User::factory()->create();

        Sanctum::actingAs($admin);

        $response = $this->getJson(
            route('users.show', [
                'user' => $user->id,
            ])
        );

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'data' => [
                    'id' => $user->id,
                    'email' => $user->email,
                ],
            ]);
    }
}