<?php

namespace Modules\Performance\Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Sanctum\Sanctum;
use Modules\Performance\Entities\PerformanceCycle;
use Modules\User\Entities\User;

class ShowCyclesTest extends TestCase
{
    use DatabaseMigrations;

    public function test_Show_Cycles_for_authenticated_users_with_role()
    {
        $admin = User::factory()->create();
        $admin->assignRole('Hr_admin');

        Sanctum::actingAs($admin);

        PerformanceCycle::factory()->count(5)->create();

        $response = $this->getJson('/api/v1/performance-cycles');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'status',
                     'message',
                     'data' => [
                         'data' => [
                             '*' => [
                                 'id',
                                 'name',
                                 'start_date',
                                 'end_date',
                                 'status',
                             ]
                         ],
                         'meta' => [
                             'current_page',
                             'last_page',
                             'per_page',
                             'total'
                         ]
                     ]
                 ]);
    }

    public function test_Show_Cycles_for_non_authenticated_users()
    {
        PerformanceCycle::factory()->count(5)->create();

        $response = $this->getJson('/api/v1/performance-cycles');

        $response->assertStatus(401)
                 ->assertJson([
                     'message' => 'Unauthenticated.'
                 ]);
    }

    public function test_Show_Cycles_for_authenticated_users_without_role()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        PerformanceCycle::factory()->count(5)->create();

        $response = $this->getJson('/api/v1/performance-cycles');

        $response->assertStatus(403)
                 ->assertJson([
                     'message' => 'Forbidden. You do not have the required permissions.'
                 ]);
    }
}
