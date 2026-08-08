<?php

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\User\Entities\User; 
use Modules\Employee\Entities\Employee;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

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

        $hrRole = Role::firstOrCreate(['name' => 'Hr_admin', 'guard_name' => 'sanctum']);
        $managerRole = Role::firstOrCreate(['name' => 'Manager', 'guard_name' => 'sanctum']);
        $employeeRole = Role::firstOrCreate(['name' => 'Employee', 'guard_name' => 'sanctum']);

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
                'first_name'      => 'Manager',
                'last_name'       => 'User',
                'department_id'   => 1,
                'status'          => 'Active',
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
                'first_name'      => 'John',
                'last_name'       => 'Doe',
                'department_id'   => $managerEmployee->department_id,
                'manager_id'      => $managerEmployee->id,
                'status'          => 'Active',
            ]
        );

        // 4. Random Employees Linked to the Manager
        User::factory()
            ->count(10)
            ->create()
            ->each(function ($user) use ($employeeRole, $managerEmployee) {
                $user->assignRole($employeeRole);
                Employee::factory()->create([
                    'user_id'       => $user->id,
                    'manager_id'    => $managerEmployee->id,
                    'department_id' => $managerEmployee->department_id,
                ]);
            });
    }
}