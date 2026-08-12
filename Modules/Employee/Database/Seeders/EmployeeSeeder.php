<?php

namespace Modules\Employee\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\Employee\Entities\Employee;
use Modules\User\Entities\User;
use Modules\Organization\Entities\Department;
use Modules\Organization\Entities\JobTitle;
use Spatie\Permission\Models\Role;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Department
        $department = Department::firstOrCreate(
            ['code' => 'GEN-01'],
            ['name' => 'General Department']
        );

        // 2. Job Title
        $jobTitle = JobTitle::firstOrCreate(
            [
                'title' => 'Software Engineer',
                'department_id' => $department->id,
            ]
        );

        // 3. HR Admin User & Employee
        $hrUser = User::firstOrCreate(
            ['email' => 'hr@company.com'],
            [
                'password'  => bcrypt('password'),
                'is_active' => true,
            ]
        );
        
        $hrRole = Role::where('name', 'Hr_admin')->where('guard_name', 'sanctum')->first();
        if ($hrRole) {
            $hrUser->assignRole($hrRole);
        }

        Employee::firstOrCreate(
            ['user_id' => $hrUser->id],
            [
                'department_id'   => $department->id,
                'job_title_id'    => $jobTitle->id,
                'employee_number' => 'EMP-001',
                'first_name'      => 'HR',
                'last_name'       => 'Admin',
                'national_id'     => '1000000001',
                'birth_date'      => '1990-01-01',
                'gender'          => 'male',
                'employment_type' => 'full_time',
                'status'          => 'active',
                'hire_date'       => now(),
            ]
        );

        // 4. Manager User & Employee
        $managerUser = User::firstOrCreate(
            ['email' => 'manager@company.com'],
            [
                'password'  => bcrypt('password'),
                'is_active' => true,
            ]
        );
        $managerRole = Role::where('name', 'Manager')->where('guard_name', 'sanctum')->first();
        if ($managerRole) {
            $managerUser->assignRole($managerRole);
        }

        $managerEmployee = Employee::firstOrCreate(
            ['user_id' => $managerUser->id],
            [
                'department_id'   => $department->id,
                'job_title_id'    => $jobTitle->id,
                'employee_number' => 'EMP-002',
                'first_name'      => 'General',
                'last_name'       => 'Manager',
                'national_id'     => '1000000002',
                'birth_date'      => '1988-05-15',
                'gender'          => 'male',
                'employment_type' => 'full_time',
                'status'          => 'active',
                'hire_date'       => now(),
                'manager_id'      => null,
            ]
        );

        // 5. Employee User & Employee
        $empUser = User::firstOrCreate(
            ['email' => 'employee@company.com'],
            [
                'password'  => bcrypt('password'),
                'is_active' => true,
            ]
        );
        $empRole = Role::where('name', 'Employee')->where('guard_name', 'sanctum')->first();
        if ($empRole) {
            $empUser->assignRole($empRole);
        }

        Employee::firstOrCreate(
            ['user_id' => $empUser->id],
            [
                'department_id'   => $department->id,
                'job_title_id'    => $jobTitle->id,
                'employee_number' => 'EMP-003',
                'first_name'      => 'John',
                'last_name'       => 'Doe',
                'national_id'     => '1000000003',
                'birth_date'      => '1995-01-01',
                'gender'          => 'male',
                'employment_type' => 'full_time',
                'status'          => 'active',
                'hire_date'       => now(),
                'manager_id'      => $managerEmployee->id,
            ]
        );
    }
}
