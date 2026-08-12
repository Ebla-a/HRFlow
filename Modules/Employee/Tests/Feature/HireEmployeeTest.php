<?php

namespace Modules\Employee\Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;

use Modules\Employee\App\Events\EmployeeHired;
use Modules\Employee\App\Enums\EmployeeStatus;
use Modules\Employee\App\Enums\EmploymentType;

use App\Models\User;
use Modules\Employee\Events\EmployeeHired as EventsEmployeeHired;
use Modules\Organization\Entities\Department;
use Modules\Organization\Entities\JobTitle;

class HireEmployeeTest extends TestCase
{
    use RefreshDatabase;

    protected User $hrAdmin;
    protected Department $department;
    protected JobTitle $jobTitle;

    protected function setUp(): void
    {
        parent::setUp();

        $this->hrAdmin = User::factory()->create();
        $this->hrAdmin->assignRole('HR Admin');

        $this->department = Department::factory()->create();
        $this->jobTitle = JobTitle::factory()->create([
            'department_id' => $this->department->id,
        ]);
    }

    public function test_hr_admin_can_hire_a_new_employee_successfully(): void
    {
        Event::fake();

        $payload = [
            'email' => 'new.employee@hrflow.test',
            'first_name' => 'Ebla',
            'last_name' => 'Zyab',
            'department_id' => $this->department->id,
            'job_title_id' => $this->jobTitle->id,
            'employee_number' => 'EMP-1001',
            'employment_type' => EmploymentType::FULL_TIME->value,
            'status' => EmployeeStatus::ACTIVE->value,
            'hire_date' => now()->format('Y-m-d'),
        ];

        $response = $this->actingAs($this->hrAdmin)
            ->postJson('/api/v1/employees', $payload, ['X-Request-ID' => 'test-uuid']);

        $response->assertStatus(201)
            ->assertJsonPath('data.employee_number', 'EMP-1001')
            ->assertJsonPath('data.full_name', 'Ebla Zyab');

        $this->assertDatabaseHas('users', ['email' => 'new.employee@hrflow.test']);
        $this->assertDatabaseHas('employees', ['employee_number' => 'EMP-1001']);

        Event::assertDispatched(EventsEmployeeHired::class);
    }

    public function test_cannot_assign_job_title_from_another_department(): void
    {
        $anotherDepartment = Department::factory()->create();

        $payload = [
            'email' => 'mismatch@hrflow.test',
            'first_name' => 'Aya',
            'last_name' => 'Ali',
            'department_id' => $anotherDepartment->id,
            'job_title_id' => $this->jobTitle->id,
            'employee_number' => 'EMP-1002',
            'employment_type' => EmploymentType::FULL_TIME->value,
            'status' => EmployeeStatus::ACTIVE->value,
            'hire_date' => now()->format('Y-m-d'),
        ];

        $response = $this->actingAs($this->hrAdmin)
            ->postJson('/api/v1/employees', $payload);

        $response->assertStatus(422);
    }

    public function test_unauthorized_users_cannot_hire_employees(): void
    {
        $employeeUser = User::factory()->create();
        $employeeUser->assignRole('Employee');

        $payload = [
            'email' => 'forbidden@hrflow.test',
            'first_name' => 'Test',
            'last_name' => 'User',
            'department_id' => $this->department->id,
            'job_title_id' => $this->jobTitle->id,
            'employee_number' => 'EMP-1003',
            'employment_type' => EmploymentType::FULL_TIME->value,
            'status' => EmployeeStatus::ACTIVE->value,
            'hire_date' => now()->format('Y-m-d'),
        ];

        $response = $this->actingAs($employeeUser)
            ->postJson('/api/v1/employees', $payload);

        $response->assertStatus(403);
    }
}