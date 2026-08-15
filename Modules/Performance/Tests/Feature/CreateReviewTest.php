<?php

namespace Modules\Performance\Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Modules\Employee\Entities\Employee;
use Modules\Organization\Entities\Department;
use Modules\Organization\Entities\JobTitle;
use Modules\Performance\Entities\PerformanceCycle;
use Modules\User\Entities\User;
use Modules\Performance\Entities\PerformanceReview;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CreateReviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_Create_review()
    {
        // Create permissions
        Permission::firstOrCreate(['name' => 'create.review.employee.own.department', 'guard_name' => 'sanctum']);
        Permission::firstOrCreate(['name' => 'view.reviews.all', 'guard_name' => 'sanctum']);

        // Create role and attach permissions
        $role = Role::firstOrCreate(['name' => 'Manager', 'guard_name' => 'sanctum']);
        $role->givePermissionTo(['create.review.employee.own.department', 'view.reviews.all']);

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

        // API request
        $response = $this->postJson('/api/v1/performance-reviews', [
            'employee_id'           => $employee->id,
            'performance_cycle_id'  => $cycle->id,
            'reviewer_id'           => $manager->id,
            'score'                 => 5,
            'comments'              => "Excellent performance",
        ]);

        $response->assertStatus(201);
    }
}
