<?php

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\User\Entities\User; 
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

        // hr account
        $hrUser = User::firstOrCreate(
            ['email' => 'hr@company.com'],
            [
                'password' => Hash::make('password123'),
                'is_active' => true,
            ]
        );
        $hrUser->assignRole($hrRole);

        // employee account
        $employeeUser = User::firstOrCreate(
            ['email' => 'employee@company.com'],
            [
                'password' => Hash::make('password123'),
                'is_active' => true,
            ]
        );
        $employeeUser->assignRole($employeeRole);

        // manager account
        $managerUser = User::firstOrCreate(
            ['email' => 'manager@company.com'],
            [
                'password' => Hash::make('password123'),
                'is_active' => true,
            ]
        );
        $managerUser->assignRole($managerRole);

        User::factory()
            ->count(10)
            ->create()
            ->each(fn($user) => $user->assignRole($employeeRole));
    }
}