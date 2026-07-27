<?php

namespace Modules\Auth\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeederTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
//delete cached roles and permissions for fresh seeding
     app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
//hr
'employees.view.all','employee.create','employee.update.all','employee.active','employee.inActive','employee.change.status',

'department.create','department.update','department.delete','departments.view.all',
'jobtitle.create','jobtitle.update','jobtitle.delete','jobtitle.view.all',
'view.documents.employee.all','upload.documents.employee.all','delete.documents.employee.all',


'leave.type.create','leave.type.update','leave.type.delete','leave.type.view.all',
'leave.request.create','leave.request.update','leave.request.delete','leave.request.view.all',
'leave.request.approve','leave.request.reject','leave.request.view.department','leave.request.view.own',
'leave.balance.view.own','leave.balance.view.department','leave.balance.view.all',

'attendence.view.all','attendence.add.note','attendence.view.history','attendence.check.in','attendence.check.out','attendence.correct','attendence.request.approve','attendence.request.reject','attendence.request.view.all',
'create.structure.salary','update.structure.salary','delete.structure.salary','view.structure.salary',
'create.salary.history','update.salary.history','delete.salary.history','view.salary.history',
'create.payroll.run','update.payroll.run','delete.payroll.run','view.payroll','finalize.payroll.run',
'generate.payslip','update.payslip','delete.payslip','view.payslip.all','view.payslip.own',
'view.salary.structure.own','view.salary.history.own',
'create.performance.cycle','update.performance.cycle','delete.performance.cycle','view.performance.cycle',
'submit.performance.review','update.performance.review','delete.performance.review','view.performance.review.all',

'send.notification','view.notification.all','view.notification.own',
'create.report','update.report','delete.report','view.reports.all','view.report.department','export.report',

//manager
  'view.department.employees','view.profile.employee.department','update.profile.employee.department','view.employee.documents.department',

            'view.leave.request.department','view.leave.balance.employee', 'leave.approve','leave.reject',
            'view.attendence.department','add.note.attendence.employee','view.attendence.history.employee',
            'performance.review.create.department','performance.review.update.department','performance.view.department',

            'approve leave request manager', 'view leave balance',
            'submit performance review', 'view own payslip',
            'report.view.department',
            'notification.send.department',


  //employee
    'employee.view.own.profile', 'employee.update.own.profile','employee.change.password',
            'create.leave.request', 'view.leave.balance', 'cancel.leave.request.own','view.leave.request.own','view.leave.history.own',
            'attendence.view.own','attendence.check.in','attendence.check.out','attendence.view.own.history',
             'view.payslip.own','view.salary.structure.own','view.salary.history.own',
             'view.performance.reviews.own',
             'notification.view.own',
             'upload.document.own', 'view.document.own',


        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'sanctum']);
        }

        $hrAdminRole = Role::firstOrCreate(['name' => 'hr_admin', 'guard_name' => 'sanctum']);
        $hrAdminRole->givePermissionTo(Permission::all());

        $managerRole = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'sanctum']);
        $managerRole->givePermissionTo([

            'view.department.employees','view.profile.employee.department','update.profile.employee.department','view.employee.documents.department',

            'view.leave.request.department','view.leave.balance.employee', 'leave.approve','leave.reject',
            'view.attendence.department','add.note.attendence.employee','view.attendence.history.employee',
            'performance.review.create.department','performance.review.update.department','performance.view.department',

            'approve leave request manager', 'view leave balance',
            'submit performance review', 'view own payslip',
            'report.view.department',
            'notification.send.department',
        ]);

        $employeeRole = Role::firstOrCreate(['name' => 'employee', 'guard_name' => 'sanctum']);

        $employeeRole->givePermissionTo([

            'employee.view.own.profile', 'employee.update.own.profile','employee.change.password',
            'create.leave.request', 'view.leave.balance', 'cancel.leave.request.own','view.leave.request.own','view.leave.history.own',
            'attendence.view.own','attendence.check.in','attendence.check.out','attendence.view.own.history',
             'view.payslip.own','view.salary.structure.own','view.salary.history.own',
             'view.performance.reviews.own',
             'notification.view.own',
             'upload.document.own', 'view.document.own',
        ]);
        }
}
