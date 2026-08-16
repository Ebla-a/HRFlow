<?php

declare(strict_types=1);

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Model::unguard();

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $hrPermissions = [
            'users.manage.all',
            'roles.manage',
            'permissions.manage',
            'roles.create',
            'roles.delete',
            'roles.grant',
            'roles.revoke',

            'permissions.create',
            'permissions.delete',
            'permissions.grant',
            'permissions.revoke',

            'create.user',
            'update.user',
            'user.active',
            'user.inActive',
            'view.users.all',

            'employees.view.all',
            'employee.create',
            'hire.employee',
            'employee.update',
            'employee.change.status',

            'departments.view',
            'departments.create',
            'departments.show',
            'departments.update',
            'departments.delete',
            'departments.assign-manager',
            'departments.force-delete',
            'departments.restore',

            'jobtitle.create',
            'jobtitle.update',
            'jobtitle.delete',
            'jobtitles.view.all',
            'jobtitles.restore',

            'leave.type.create',
            'leave.type.update',
            'leave.type.delete',
            'leave.types.view.all',
            'leave.requests.view.all',
            'leave.balance.view.all',

            'view.documents.employee.all',
            'upload.documents.employee.all',
            'delete.documents.employee.all',

            'attendence.view.all',
            'attendence.add.note',
            'attendence.view.history',
            'attendence.correct',
            'attendence.request.approve',
            'attendence.request.reject',
            'attendence.request.view.all',

            'create.structure.salary',
            'update.structure.salary',
            'delete.structure.salary',
            'view.structure.salary.all',

            'create.salary.history',
            'update.salary.history',
            'delete.salary.history',
            'view.salary.history.all',

            'create.payroll.run',
            'update.payroll.run',
            'delete.payroll.run',
            'view_payroll_runs',
            'finalize.payroll.run',

            'generate.payslip',
            'update.payslip',
            'delete.payslip',
            'view.payslip.all',

            'create.performance.cycle',
            'update.performance.cycle',
            'delete.performance.cycle',
            'view.performance.cycle.all',

            'view.reviews.all',

            'view.notification.all',
            'view.notification.own',

            'create.report',
            'update.report',
            'delete.report',
            'view.reports.all',
            'export.report',

        'view.exchange.rates',
        ];

        $managerPermissions = [
            'view.department.employees.own',
            'view.profile.employee.own',
            'view.employee.documents.own',

            'departments.show',

            'view.leave.request.department',
            'view.leave.balance.employee',
            'leave.approve',
            'leave.reject',
            'view.leave.types',

            'view.attendence.department',
            'add.note.attendence.employee',
            'view.attendence.history.employee',

            'create.review.employee.own.department',
            'update.review.employee.own.department',
            'view.reviews.department',

            'report.view.department',
        ];

        $employeePermissions = [
            'employee.view.own.profile',
            'employee.update.own.profile',
            'employee.change.password',

            'create.leave.request',
            'view.leave.request',
            'view.leave.balance',
            'cancel.leave.request.own',
            'view.leave.request.own',
            'view.leave.history.own',

            'attendence.view.own',
            'attendence.check.in',
            'attendence.check.out',
            'attendence.view.own.history',

            'view.payslip.own',
            'view.payslip.deducations.own',
            'view.salary.structure.own',
            'view.salary.history.own',

            'view.performance.reviews.own',

            'notification.view.own',

            'upload.document.own',
            'view.document.own',
        ];

        $allPermissions = array_values(
            array_unique(
                array_merge(
                    $employeePermissions,
                    $managerPermissions,
                    $hrPermissions
                )
            )
        );

        foreach ($allPermissions as $permissionName) {
            Permission::updateOrCreate(
                [
                    'name' => $permissionName,
                    'guard_name' => 'sanctum',
                ],
                []
            );
        }

        $hrAdminRole = Role::updateOrCreate(
            [
                'name' => 'Hr_admin',
                'guard_name' => 'sanctum',
            ],
            []
        );

        $hrAdminRole->syncPermissions($allPermissions);

        $managerRole = Role::updateOrCreate(
            [
                'name' => 'Manager',
                'guard_name' => 'sanctum',
            ],
            []
        );

        $managerRole->syncPermissions(
            array_values(
                array_unique(
                    array_merge(
                        $employeePermissions,
                        $managerPermissions
                    )
                )
            )
        );

        $employeeRole = Role::updateOrCreate(
            [
                'name' => 'Employee',
                'guard_name' => 'sanctum',
            ],
            []
        );

        $employeeRole->syncPermissions($employeePermissions);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}

