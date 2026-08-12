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

class CreateRevieTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_Create_review()
    {
        
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $this->app->make(PermissionRegistrar::class)->forgetCachedPermissions();
        $permission=Permission::create([
            'name'=>'create.review.employee.own.department','guard_name' => 'sanctum'
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

        $response=$this->postJson('/api/v1/performance-reviews',
        [
            'employee_id'=>$employee->id ,
            'performance_cycle_id'=>$cycle->id ,
            'reviewer_id'=>$admin->id,
            'score'=>5,
            'comments'=>"ssssssssssssssss",

        ]);
        $review = PerformanceReview::first();
        $response
        ->assertStatus(201)
        ->assertJsonStructure([
                'status',
                'message',
                'data' =>[
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
        ])
        ->assertJson([
            'status'=> true,
            'message'=> "Performance review created successfully.",
                'data' =>[
                    'id'=> 1,
                    'cycle_id' => $cycle->id,
                    'employee_id'=> $employee->id ,
                    'manager_id' =>$admin->id,
                    'status'=> "Reviewed",
                    'score'=> 5,
                    'comments'=> "ssssssssssssssss",
                    'reviewed_at'=>$review->reviewed_at->format('y-m-d 00:00:00') ,
                    'status_cycle'=> "Active",
                    'cycle_name'=>$cycle->name,
                    'employee_name'=>$employee->first_name,
                ]
        ])
        ;
    }



    public function test_Create_review_with_bad_comment()
    {
        
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $this->app->make(PermissionRegistrar::class)->forgetCachedPermissions();
        $permission=Permission::create([
            'name'=>'create.review.employee.own.department','guard_name' => 'sanctum'
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

        $response=$this->postJson('/api/v1/performance-reviews',
        [
            'employee_id'=>$employee->id ,
            'performance_cycle_id'=>$cycle->id ,
            'reviewer_id'=>$admin->id,
            'score'=>5,
            'comments'=>"badword1",

        ]);
        $review = PerformanceReview::first();
        $response
        ->assertStatus(422)
        ->assertJsonStructure([
                'status',
                'message',
                'errors' =>[
                    'comments'
                ]
        ])
        ->assertJson([
            'status'=> false,
            'message'=> "Validation errors",
                'errors' =>[
                    'comments'=>[
                        'The comments contain inappropriate words.'
                    ]
                ]
        ])
        ;
    }

}
