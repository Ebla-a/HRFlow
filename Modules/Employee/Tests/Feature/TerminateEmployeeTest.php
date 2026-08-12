<?php

namespace Modules\Employee\Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;

use Modules\Employee\App\Events\EmployeeTerminated;
use Modules\Employee\App\Enums\EmployeeStatus;
use App\Models\User;
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

        $this->hrAdmin = User::factory()->create();
        $this->hrAdmin->assignRole('HR Admin');

        $user = User::factory()->create(['is_active' => true]);
        $this->employee = Employee::factory()->create([
            'user_id' => $user->id,
            'status' => EmployeeStatus::ACTIVE->value,
        ]);
    }

    public function test_hr_admin_can_terminate_employee_and_deactivate_user(): void
    {
        Event::fake();

        $payload = [
            'termination_reason' => 'End of contract',
        ];

        $response = $this->actingAs($this->hrAdmin)
            ->postJson("/api/v1/employees/{$this->employee->id}/terminate", $payload);

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