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

class MyReviewsTest extends TestCase
{
    use DatabaseMigrations;

    public function test_My_Reviews()
    {
        $managerUser = User::factory()->create();
        $managerUser->assignRole('Manager');

        $employeeUser = User::factory()->create();
        $employeeUser->assignRole('Employee');

        $department = Department::factory()->create();
        $jobTitle = JobTitle::factory()->create(['department_id' => $department->id]);

        $manager = Employee::factory()->create([
            'user_id' => $managerUser->id,
            'department_id' => $department->id,
            'job_title_id' => $jobTitle->id,
        ]);

        $employee = Employee::factory()->create([
            'user_id' => $employeeUser->id,
            'department_id' => $department->id,
            'job_title_id' => $jobTitle->id,
            'manager_id' => $manager->id,
        ]);

        $cycle = PerformanceCycle::factory()->create(['status' => 'Closed']);

        PerformanceReview::factory()->create([
            'employee_id' => $employee->id,
            'reviewer_id' => $manager->id,
            'performance_cycle_id' => $cycle->id,
            'status' => 'Reviewed',
            'score' => 5,
            'comments' => 'AAAA',
        ]);

        Sanctum::actingAs($employeeUser);

        $response = $this->getJson('/api/v1/performance-reviews/my');

        $response->assertStatus(200);
    }
}
