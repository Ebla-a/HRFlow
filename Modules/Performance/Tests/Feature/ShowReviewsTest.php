<?php

namespace Modules\Performance\Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Sanctum\Sanctum;
use Modules\User\Entities\User;
use Modules\Performance\Entities\PerformanceReview;
use Modules\Performance\Entities\PerformanceCycle;
use Modules\Employee\Entities\Employee;
use Modules\Organization\Entities\Department;
use Modules\Organization\Entities\JobTitle;

class ShowReviewsTest extends TestCase
{
    use DatabaseMigrations;

    public function test_Show_Reviews()
    {
        $managerUser = User::factory()->create();
        $managerUser->assignRole('Manager');

        Sanctum::actingAs($managerUser);

        $employeeUser = User::factory()->create();

        $department = Department::factory()->create();
        $jobTitle = JobTitle::factory()->create(['department_id' => $department->id]);

        $employee = Employee::factory()->create([
            'user_id' => $employeeUser->id,
            'department_id' => $department->id,
            'job_title_id' => $jobTitle->id,
        ]);
        $managerEmployee = Employee::factory()->create([
    'user_id' => $managerUser->id,
    'department_id' => $department->id,
    'job_title_id' => $jobTitle->id,
]);


        $cycle = PerformanceCycle::factory()->create(['status' => 'Active']);

        PerformanceReview::factory()->create([
            'employee_id' => $employee->id,
            'reviewer_id' => $managerEmployee->id,
            'performance_cycle_id' => $cycle->id,
            'status' => 'Reviewed',
            'score' => 5,
            'comments' => 'Good',
        ]);

        $response = $this->getJson('/api/v1/performance-reviews');

        $response->assertStatus(200);
    }
}
