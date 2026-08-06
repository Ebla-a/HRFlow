<?php

namespace Modules\Employee\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\Employee\Entities\Employee;
use Modules\Organization\Entities\Department;
use Modules\Organization\Entities\JobTitle;


class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $employeeUser = User::where('email', 'employee@company.com')->first();

        $department = Department::first();
        $jobTitle = JobTitle::first();

        Employee::firstOrCreate(
            [
                'user_id' => $employeeUser->id,
            ],
            [
                'department_id' => $department->id,
                'job_title_id' => $jobTitle->id,

                'employee_number' => 'EMP-001',

                'first_name' => 'John',
                'last_name' => 'Doe',

                'national_id' => '123456789',

                'birth_date' => '1995-01-01',

                'gender' => 'male',

                'employment_type' => 'full_time',

                'status' => 'active',

                'hire_date' => now(),
            ]
        );
    }
}
