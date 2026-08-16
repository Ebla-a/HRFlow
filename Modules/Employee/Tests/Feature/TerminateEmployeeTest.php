<?php

namespace Modules\Employee\Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\PermissionRegistrar;

use Modules\Employee\App\Enums\EmployeeStatus;
use Modules\User\Entities\User;
use Modules\Employee\Entities\Employee;
use Modules\Employee\Events\EmployeeTerminated as EventsEmployeeTerminated;

class TerminateEmployeeTest extends TestCase
{
    use RefreshDatabase;

    protected User $hrAdmin;
    protected Employee $employee;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Modules\User\Database\Seeders\RolesAndPermissionsSeeder::class);


        $this->hrAdmin = User::factory()->create();
        $this->hrAdmin->guard_name = 'sanctum';
        $this->hrAdmin->assignRole('Hr_admin');

        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        $user = User::factory()->create(['is_active' => true]);

        $this->employee = Employee::factory()->create([
            'user_id' => $user->id,
            'status' => EmployeeStatus::ACTIVE->value,
        ]);
    }

    public function test_hr_admin_can_terminate_employee_and_deactivate_user(): void
    {
        $this->withoutExceptionHandling();
        Event::fake();

        $payload = [
            'termination_reason' => 'End of contract',
        ];

        Sanctum::actingAs($this->hrAdmin, ['*'], 'sanctum');

        $response = $this->postJson("/api/v1/employees/{$this->employee->id}/terminate", $payload);

        $response->assertStatus(200)
            ->assertJsonPath('data.status', EmployeeStatus::TERMINATED->value);

        $this->assertDatabaseHas('employees', [
            'id' => $this->employee->id,
            'status' => EmployeeStatus::TERMINATED->value,
            'termination_reason' => 'End of contract',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $this->employee->user_id,
            'is_active' => false,
        ]);

        Event::assertDispatched(EventsEmployeeTerminated::class);
    }
}
