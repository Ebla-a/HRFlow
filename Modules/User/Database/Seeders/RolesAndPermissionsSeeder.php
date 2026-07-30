<?php

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Create permissions
        Permission::firstOrCreate(['name' => 'edit articles']);
        Permission::firstOrCreate(['name' => 'delete articles']);

        // 3. Create roles and assign existing permissions
        $roleAdmin = Role::firstOrCreate(['name' => 'Hr_Admin']);
        $roleAdmin->givePermissionTo(Permission::all());

        $roleUser = Role::firstOrCreate(['name' => 'Employee']);
        $roleUser->givePermissionTo('edit articles');
    }
}