<?php

namespace Modules\Performance\Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Sanctum\Sanctum;
use Modules\User\Entities\User;
use Modules\Employee\Entities\Employee;
use Modules\Organization\Entities\Department;
use Modules\Organization\Entities\JobTitle;
use Modules\Performance\Entities\PerformanceCycle;
use Modules\Performance\Entities\PerformanceReview;

class UpdateReviewTest extends TestCase
{
    use DatabaseMigrations;

    public function test_Update_Review_()
    {
        $manager = User::factory()->create();
        $manager->givePermissionTo('update.review.employee.own.department');

        $managerEmployee = Employee::factory()->create([
            'user_id' => $manager->id,
        ]);

        $manager->setRelation('employee', $managerEmployee);

        $cycle = PerformanceCycle::factory()->create([
            'status' => 'Active',
        ]);

        $review = PerformanceReview::factory()->create([
            'reviewer_id' => $managerEmployee->id,
            'performance_cycle_id' => $cycle->id,
        ]);

        $response = $this->actingAs($manager)->putJson(route('UpdateReview', $review->id), [
            'score' => 1,
            'comments' => 'F',
        ]);

        $response->assertStatus(200)
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

    public function test_Update_Review_without_manager_role()
    {
        // Employee user (NOT manager)
        $employeeUser = User::factory()->create();
        $employeeUser->assignRole('Employee');

        Sanctum::actingAs($employeeUser);

        // Manager user
        $managerUser = User::factory()->create();

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

        $managerUser->setRelation('employee', $manager);

        // Employee record
        $employee = Employee::factory()->create([
            'user_id'        => $employeeUser->id,
            'department_id'  => $department->id,
            'job_title_id'   => $jobTitle->id,
            'manager_id'     => $manager->id,
        ]);

        // Active cycle
        $cycle = PerformanceCycle::factory()->create(['status' => 'Active']);

        // Review
        $review = PerformanceReview::factory()->create([
            'employee_id'          => $employee->id,
            'reviewer_id'          => $manager->id,
            'performance_cycle_id' => $cycle->id,
            'status'               => 'Reviewed',
            'score'                => 5,
            'comments'             => 'AAAA',
        ]);

        // API request
        $response = $this->putJson("/api/v1/performance-reviews/{$review->id}", [
            'score'    => 1,
            'comments' => 'F',
        ]);

        $response->assertStatus(403)
                 ->assertJson([
                     'success' => false,
                     'message' => "Forbidden. You do not have the required permissions.",
                 ]);
    }
}