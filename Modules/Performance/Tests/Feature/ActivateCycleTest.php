<?php

namespace Modules\Performance\Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Sanctum\Sanctum;
use Modules\User\Entities\User;
use Modules\Performance\Entities\PerformanceCycle;

class ActivateCycleTest extends TestCase
{
    use DatabaseMigrations;

    public function test_Active_Cycle_Hr_admin_can_active_the_cycle()
    {
        $admin = User::factory()->create();
        $admin->assignRole('Hr_admin');

        Sanctum::actingAs($admin);

        $performance = PerformanceCycle::factory()->create();

        $response = $this->postJson("/api/v1/performance-cycles/{$performance->id}/activate");

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'status',
                     'message',
                     'data' => [
                         'id',
                         'name',
                         'start_date',
                         'end_date',
                         'status',
                     ]
                 ]);
    }

    public function test_Active_Cycle_Hr_admin_can_close_the_cycle()
    {
        $admin = User::factory()->create();
        $admin->assignRole('Hr_admin');

        Sanctum::actingAs($admin);

        $performance = PerformanceCycle::factory()->create(['status' => 'Active']);

        $response = $this->postJson("/api/v1/performance-cycles/{$performance->id}/close");

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'status',
                     'message',
                     'data' => [
                         'id',
                         'name',
                         'start_date',
                         'end_date',
                         'status',
                     ]
                 ]);
    }

    public function test_Active_Cycle_Hr_admin_can_not_active_the_cycle_if_the_date_pass()
    {
        $admin = User::factory()->create();
        $admin->assignRole('Hr_admin');

        Sanctum::actingAs($admin);

        $performance = PerformanceCycle::factory()->create([
            'start_date' => "2026-09-01 00:00:00",
            'end_date'   => "2026-01-10 00:00:00",
        ]);

        $response = $this->postJson("/api/v1/performance-cycles/{$performance->id}/activate");

        $response->assertStatus(422)
                 ->assertJson([
                     'status'  => false,
                     'message' => 'Cannot activate a performance cycle whose end date has passed.',
                 ]);
    }
}
