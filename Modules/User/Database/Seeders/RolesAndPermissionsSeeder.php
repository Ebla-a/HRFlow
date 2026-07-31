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
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        /**
         * USER MANAGEMENT PERMISSIONS
         */
        $permissions = [
            'users.view',
            'users.view.single',
            'users.update.email',
            'users.update.avatar',
            'users.activate',
            'users.deactivate',

            /**
             * ROLE MANAGEMENT
             */
            'roles.create',
            'roles.delete',
            'roles.grant',
            'roles.revoke',

            /**
             * PERMISSION MANAGEMENT
             */
            'permissions.create',
            'permissions.delete',
            'permissions.grant',
            'permissions.revoke',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        /**
         * ROLES
         */
        $roleAdmin = Role::firstOrCreate(['name' => 'Hr_admin']);
        $roleEmployee = Role::firstOrCreate(['name' => 'Employee']);
        $roleEmployee = Role::firstOrCreate(['name' => 'Manager ']);

        /**
         * ASSIGN PERMISSIONS TO ROLES
         */

        // Admin gets everything
        $roleAdmin->givePermissionTo(Permission::all());

        // Employee gets minimal permissions (view only)
        $roleEmployee->givePermissionTo([
            'users.view',
            'users.view.single',
        ]);
    }
}
