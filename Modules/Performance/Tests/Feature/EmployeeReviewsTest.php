<?php

namespace Modules\Performance\Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Laravel\Sanctum\Sanctum;
use Modules\Employee\Entities\Employee;
use Modules\Organization\Entities\Department;
use Modules\Organization\Entities\JobTitle;
use Modules\Performance\Entities\PerformanceCycle;
use Modules\User\Entities\User;
use Modules\Performance\Entities\PerformanceReview;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Models\Permission;

class EmployeeReviewsTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_Employee_Reviews()
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $this->app->make(PermissionRegistrar::class)->forgetCachedPermissions();
        $permission=Permission::create([
            'name'=>'view.reviews.department','guard_name' => 'sanctum'
        ]);

        $role=Role::create(['name'=>'Manager','guard_name' => 'sanctum']);
        $role->givePermissionTo($permission);
        $admin = User::factory()->create();
        $admin->assignRole($role);
        Sanctum::actingAs($admin, ['*'], 'sanctum');

        $user = User::factory()->create();
        $department=Department::factory()->create();
        $jobTitle=JobTitle::factory()->create([
            'department_id'=>$department->id
        ]);

        $manager= Employee::factory()->create([
            'user_id'=>$admin->id,
            'department_id' => $department->id,
            'job_title_id' => $jobTitle->id,
            'employee_number' => 'EMP-0012',
            'national_id' => '1234567892',
        ]);

        $employee= Employee::factory()->create([
            'user_id'=>$user->id,
            'department_id' => $department->id,
            'job_title_id' => $jobTitle->id,
            'manager_id'=>$manager->id,
            'employee_number' => 'EMP-001',
            'national_id' => '123456789',
        ]);

        $cycle=PerformanceCycle::factory()->create(['status'=>'Active']);
        $review=PerformanceReview::factory()->create([
            'employee_id'             => $employee->id,
            'reviewer_id'             => $manager->id,
            'performance_cycle_id'    => $cycle->id,
            'status'=>'Reviewed',
            'score'       => 5,
            'comments'    => 'AAAA',
        ]);
        $response=$this->getJson('/api/v1/employees/'.$employee->id.'/performance');
        
        $response
        ->assertStatus(200)
        ->assertJsonStructure([
            'status',
            'message',
            'data' => [
                'data' => [
                    '*' => [
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
                        'employee_name'
                    ]
                ],
                'meta' => [
                    'current_page',
                    'last_page',
                    'per_page',
                    'total'
                ]
            ]
        ])
        ->assertJson([
            "status"=> true,
            "message"=>"Employee reviews retrieved successfully.",
            'data'=>[
                'data'=>[
                    [ 
                    'id'=>$review->id,
                    "cycle_id"=> $cycle->id,
                    "employee_id"=> $employee->id,
                    "manager_id"=> $manager->id,
                    "status"=> "Reviewed",
                    "score"=> $review->score,
                    "comments"=> $review->comments,
                    "reviewed_at"=> $review->reviewed_at->format('y-m-d 00:00:00') ,
                    "status_cycle"=> $cycle->status,
                    "cycle_name"=> $cycle->name,
                    "employee_name"=> $employee->first_name,
                    ]
                ]
            ]
        ])
        ;


    }
}
