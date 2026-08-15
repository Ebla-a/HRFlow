<?php

namespace Modules\Performance\Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Sanctum\Sanctum;
use Modules\Employee\Entities\Employee;
use Modules\Organization\Entities\Department;
use Modules\Organization\Entities\JobTitle;
use Modules\Performance\Entities\PerformanceCycle;
use Modules\User\Entities\User;
use Modules\Performance\Entities\PerformanceReview;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class EmployeeReviewsTest extends TestCase
{
    use DatabaseMigrations;

    public function test_Employee_Reviews()
    {
        // Create permissions
        Permission::firstOrCreate(['name' => 'view.reviews.all', 'guard_name' => 'sanctum']);
        Permission::firstOrCreate(['name' => 'view.reviews.department', 'guard_name' => 'sanctum']);

        // Create role and attach permissions
        $role = Role::firstOrCreate(['name' => 'Manager', 'guard_name' => 'sanctum']);
        $role->givePermissionTo(['view.reviews.all', 'view.reviews.department']);

        // Manager user
        $managerUser = User::factory()->create();
        $managerUser->assignRole('Manager');

        Sanctum::actingAs($managerUser);

        // Employee user
        $employeeUser = User::factory()->create();

        // Department + JobTitle
        $department = Department::factory()->create();
        $jobTitle = JobTitle::factory()->create(['department_id' => $department->id]);

        // Manager employee record
        $manager = Employee::factory()->create([
            'user_id' => $managerUser->id,
            'department_id' => $department->id,
            'job_title_id' => $jobTitle->id,
        ]);

        // Employee record
        $employee = Employee::factory()->create([
            'user_id' => $employeeUser->id,
            'department_id' => $department->id,
            'job_title_id' => $jobTitle->id,
            'manager_id' => $manager->id,
        ]);

        // Active cycle
        $cycle = PerformanceCycle::factory()->create(['status' => 'Active']);

        // Review
        PerformanceReview::factory()->create([
            'employee_id' => $employee->id,
            'reviewer_id' => $manager->id,
            'performance_cycle_id' => $cycle->id,
            'status' => 'Reviewed',
            'score' => 5,
            'comments' => 'AAAA',
        ]);

        // API request
        $response = $this->getJson("/api/v1/employees/{$employee->id}/performance");

        $response->assertStatus(200);
    }
}
