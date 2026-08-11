<?php

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Modules\User\Entities\User;
use Modules\Employee\Entities\Employee;
use Modules\Organization\Entities\Department;
use Modules\Organization\Entities\JobTitle;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $hrRole = Role::firstOrCreate([
            'name' => 'Hr_admin',
            'guard_name' => 'sanctum',
        ]);

        $managerRole = Role::firstOrCreate([
            'name' => 'Manager',
            'guard_name' => 'sanctum',
        ]);

        $employeeRole = Role::firstOrCreate([
            'name' => 'Employee',
            'guard_name' => 'sanctum',
        ]);

        // Get Software Development department
        $softwareDept = Department::where('code', 'DEP-DEV')->firstOrFail();

        // Get existing job titles
        $managerJobTitle = JobTitle::where('title', 'Senior Backend Developer')
            ->where('department_id', $softwareDept->id)
            ->firstOrFail();

        $employeeJobTitle = JobTitle::where('title', 'Backend Developer')
            ->where('department_id', $softwareDept->id)
            ->firstOrFail();

        // 1. HR Account
        $hrUser = User::firstOrCreate(
            ['email' => 'hr@company.com'],
            [
                'password' => Hash::make('password123'),
                'is_active' => true,
            ]
        );

        $hrUser->assignRole($hrRole);

        // 2. Manager Account & Employee Record
        $managerUser = User::firstOrCreate(
            ['email' => 'manager@company.com'],
            [
                'password' => Hash::make('password123'),
                'is_active' => true,
            ]
        );

        $managerUser->assignRole($managerRole);

        $managerEmployee = Employee::firstOrCreate(
            ['user_id' => $managerUser->id],
            [
                'employee_number' => 'MGR-001',
                'first_name' => 'Manager',
                'last_name' => 'User',
                'department_id' => $softwareDept->id,
                'job_title_id' => $managerJobTitle->id,
                'national_id' => '1000000001',
                'birth_date' => '1990-01-01',
                'gender' => 'male',
                'employment_type' => 'full_time',
                'hire_date' => '2026-01-01',
                'status' => 'active',
            ]
        );

        // 3. Employee Account & Employee Record Linked to Manager
        $employeeUser = User::firstOrCreate(
            ['email' => 'employee@company.com'],
            [
                'password' => Hash::make('password123'),
                'is_active' => true,
            ]
        );

        $employeeUser->assignRole($employeeRole);

        Employee::firstOrCreate(
            ['user_id' => $employeeUser->id],
            [
                'employee_number' => 'EMP-001',
                'first_name' => 'John',
                'last_name' => 'Doe',
                'department_id' => $managerEmployee->department_id,
                'job_title_id' => $employeeJobTitle->id,
                'manager_id' => $managerEmployee->id,
                'national_id' => '1000000002',
                'birth_date' => '1995-01-01',
                'gender' => 'male',
                'employment_type' => 'full_time',
                'hire_date' => '2026-01-01',
                'status' => 'active',
            ]
        );

        // 4. Random Employees Linked to the Manager
        User::factory()
            ->count(10)
            ->create()
            ->each(function ($user) use (
                $employeeRole,
                $managerEmployee,
                $employeeJobTitle
            ) {
                $user->assignRole($employeeRole);

                Employee::factory()->create([
                    'user_id' => $user->id,
                    'manager_id' => $managerEmployee->id,
                    'department_id' => $managerEmployee->department_id,
                    'job_title_id' => $employeeJobTitle->id,
                ]);
            });
    }
}