<?php

namespace Modules\Employee\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Employee\Entities\Employee;
use Modules\User\Entities\User;
use Modules\Organization\Entities\Department;
use Modules\Organization\Entities\JobTitle;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $department = Department::first();
        $jobTitle   = JobTitle::first();

        // 1. Create HR Admin
        $hrUser = User::where('email', 'hr@company.com')->first();
        if ($hrUser) {
            Employee::firstOrCreate(
                ['user_id' => $hrUser->id],
                [
                    'department_id'   => $department?->id,
                    'job_title_id'    => $jobTitle?->id,
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
        }

        // 2.  create Manager
        $managerUser = User::where('email', 'manager@company.com')->first();
        if ($managerUser) {
            Employee::firstOrCreate(
                ['user_id' => $managerUser->id],
                [
                    'department_id'   => $department?->id,
                    'job_title_id'    => $jobTitle?->id,
                    'employee_number' => 'EMP-002',
                    'first_name'      => 'General',
                    'last_name'       => 'Manager',
                    'national_id'     => '1000000002',
                    'birth_date'      => '1988-05-15',
                    'gender'          => 'male',
                    'employment_type' => 'full_time',
                    'status'          => 'active',
                    'hire_date'       => now(),
                ]
            );
        }
       
        //c create employee
        $empUser = User::where('email', 'employee@company.com')->first();
        if ($empUser) {
            Employee::firstOrCreate(
                ['user_id' => $empUser->id],
                [
                    'department_id'   => $department?->id,
                    'job_title_id'    => $jobTitle?->id,
                    'employee_number' => 'EMP-003',
                    'first_name'      => 'John',
                    'last_name'       => 'Doe',
                    'national_id'     => '1000000003',
                    'birth_date'      => '1995-01-01',
                    'gender'          => 'male',
                    'employment_type' => 'full_time',
                    'status'          => 'active',
                    'hire_date'       => now(),
                ]
            );
        }
    }
}