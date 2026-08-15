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

class CreateReviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_Create_review()
    {
        // Create needed permissions
        Permission::firstOrCreate(['name' => 'create.review.employee.own.department', 'guard_name' => 'sanctum']);
        Permission::firstOrCreate(['name' => 'view.reviews.all', 'guard_name' => 'sanctum']);

        // Manager user
        $managerUser = User::factory()->create();
        $managerUser->assignRole('Manager');
        $managerUser->givePermissionTo('create.review.employee.own.department');
        $managerUser->givePermissionTo('view.reviews.all');

        Sanctum::actingAs($managerUser);

        // Employee user
        $employeeUser = User::factory()->create();

        // Department + JobTitle
        $department = Department::factory()->create();
        $jobTitle = JobTitle::factory()->create([
            'department_id' => $department->id
        ]);

        // Manager employee record
        $manager = Employee::factory()->create([
            'user_id'        => $managerUser->id,
            'department_id'  => $department->id,
            'job_title_id'   => $jobTitle->id,
        ]);

        // Employee record
        $employee = Employee::factory()->create([
            'user_id'        => $employeeUser->id,
            'department_id'  => $department->id,
            'job_title_id'   => $jobTitle->id,
            'manager_id'     => $manager->id,
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

        $response->assertStatus(201)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'id',
                    'cycle_id',
                    'employee_id',
                    'manager_id',
                    'status',
                    'score',
                    'comments',
                    'reviewed_at',
                    'status_cycle',
                    'cycle_name',
                    'employee_name',
                ]
            ]);
    }

    public function test_Create_review_with_bad_comment()
    {
        // Manager user
        $managerUser = User::factory()->create();
        $managerUser->assignRole('Manager');

        Sanctum::actingAs($managerUser);

        // Employee user
        $employeeUser = User::factory()->create();

        // Department + JobTitle
        $department = Department::factory()->create();
        $jobTitle = JobTitle::factory()->create([
            'department_id' => $department->id
        ]);

        // Manager employee record
        $manager = Employee::factory()->create([
            'user_id'        => $managerUser->id,
            'department_id'  => $department->id,
            'job_title_id'   => $jobTitle->id,
        ]);

        // Employee record
        $employee = Employee::factory()->create([
            'user_id'        => $employeeUser->id,
            'department_id'  => $department->id,
            'job_title_id'   => $jobTitle->id,
            'manager_id'     => $manager->id,
        ]);

        // Active cycle
        $cycle = PerformanceCycle::factory()->create(['status' => 'Active']);

        // API request
        $response = $this->postJson('/api/v1/performance-reviews', [
            'employee_id'           => $employee->id,
            'performance_cycle_id'  => $cycle->id,
            'reviewer_id'           => $manager->id,
            'score'                 => 5,
            'comments'              => "badword1",
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'status',
                'message',
                'errors' => ['comments']
            ])
            ->assertJson([
                'status'  => false,
                'message' => "Validation errors",
            ]);
    }
}
