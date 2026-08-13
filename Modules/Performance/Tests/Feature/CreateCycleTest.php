<?php

namespace Modules\Performance\Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Sanctum\Sanctum;
use Modules\User\Entities\User;

class CreateCycleTest extends TestCase
{
    use DatabaseMigrations;

    public function test_Create_Cycle_with_role_HR_admin()
    {
        $admin = User::factory()->create();
        $admin->assignRole('Hr_admin');

        Sanctum::actingAs($admin);

        $response = $this->postJson('/api/v1/performance-cycles', [
            'name'       => "test",
            'start_date' => "2026-09-01 00:00:00",
            'end_date'   => "2026-09-10 00:00:00",
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'status',
                     'message',
                     'data' => [
                         'id',
                         'name',
                         'start_date',
                         'end_date',
                         'status',
                     ],
                 ]);
    }

    public function test_Create_Cycle_with_role_HR_admin_with_bad_word_and_passed_start_day()
    {
        $admin = User::factory()->create();
        $admin->assignRole('Hr_admin');

        Sanctum::actingAs($admin);

        $response = $this->postJson('/api/v1/performance-cycles', [
            'name'       => "badword1",
            'start_date' => "2026-05-01 00:00:00",
            'end_date'   => "2026-03-10 00:00:00",
        ]);

        $response->assertStatus(422)
                 ->assertJsonStructure([
                     'status',
                     'message',
                     'errors'
                 ]);
    }
}
