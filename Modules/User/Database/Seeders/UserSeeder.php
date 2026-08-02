<?php

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
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


 $hrRole = Role::firstOrCreate(['name' => 'HR Admin', 'guard_name' => 'api']);
$managerRole = Role::firstOrCreate(['name' => 'Manager', 'guard_name' => 'api']);
$employeeRole = Role::firstOrCreate(['name' => 'Employee', 'guard_name' => 'api']);


        // اhr account
        $hrUser = User::firstOrCreate(
            ['email' => 'hr@company.com'],
            [
                'name' => 'HR Manager',
                'password' => Hash::make('password123'),
                'is_active' => true,
            ]
        );
        $hrUser->assignRole($hrRole);

        //employee account
        $employeeUser = User::firstOrCreate(
            ['email' => 'employee@company.com'],
            [
                'name' => 'John Doe',
                'password' => Hash::make('password123'),
                'is_active' => true,
            ]
        );
        $employeeUser->assignRole($employeeRole);




//manager account
$managerUser = User::firstOrCreate(
    ['email' => 'manager@company.com'],
    [
        'name' => 'IT Department Manager',
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
