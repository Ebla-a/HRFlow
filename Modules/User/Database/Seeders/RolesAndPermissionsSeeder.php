<?php 
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
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
//delete cached roles and permissions for fresh seeding
     app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $hr_permissions = [
            'roles.create',
            'roles.delete',
            'roles.grant',
            'roles.revoke',

            'permissions.create',
            'permissions.delete',
            'permissions.grant',
            'permissions.revoke',



'create.user','update.user','user.active','user.inActive','view.users.all',
'employees.view.all','employee.create','employee.update','employee.change.status',

'department.create','department.update','department.delete','departments.view.all',
'jobtitle.create','jobtitle.update','jobtitle.delete','jobtitles.view.all',
'leave.type.create','leave.type.update','leave.type.delete','leave.types.view.all',
'leave.requests.view.all',

'leave.balance.view.all',

'view.documents.employee.all','upload.documents.employee.all','delete.documents.employee.all',


'attendence.view.all','attendence.add.note','attendence.view.history',
'attendence.correct','attendence.request.approve','attendence.request.reject','attendence.request.view.all',

'create.structure.salary','update.structure.salary','delete.structure.salary','view.structure.salary.all',
'create.salary.history','update.salary.history','delete.salary.history','view.salary.history.all',

'create.payroll.run','update.payroll.run','delete.payroll.run','view.payroll','finalize.payroll.run',
'generate.payslip','update.payslip','delete.payslip','view.payslip.all',

'create.performance.cycle','update.performance.cycle','delete.performance.cycle','view.performance.cycle.all',
'view.reviews.all',

'view.notification.all','view.notification.own',
'create.report','update.report','delete.report','view.reports.all','export.report',

        ];


        $manager_permissions = [
    'view.department.employees.own','view.profile.employee.own','view.employee.documents.own',

            'view.leave.request.department','view.leave.balance.employee', 'leave.approve','leave.reject',
            'view.leave.types',
            'view.attendence.department','add.note.attendence.employee','view.attendence.history.employee',
            'create.review.employee.own.department','update.review.employee.own.department','view.reviews.department',
            'report.view.department',

        ];

        $employee_permissions = [
   'employee.view.own.profile', 'employee.update.own.profile','employee.change.password',
            'create.leave.request','view.leave.request', 'view.leave.balance', 'cancel.leave.request.own','view.leave.request.own','view.leave.history.own',
            'attendence.view.own','attendence.check.in','attendence.check.out','attendence.view.own.history',
             'view.payslip.own', 'view.payslip.deducations.own','view.salary.structure.own','view.salary.history.own',
             'view.performance.reviews.own',
             'notification.view.own',
             'upload.document.own', 'view.document.own',


        ];

        $allPermissions = array_unique(array_merge($employee_permissions, $manager_permissions, $hr_permissions));
        foreach ($allPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'sanctum']);
        }

        $hrAdminRole = Role::firstOrCreate(['name' => 'hr_admin', 'guard_name' => 'sanctum']);
        $hrAdminRole->givePermissionTo($allPermissions);

        $managerRole = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'sanctum']);
      $managerRole->givePermissionTo(array_merge($employee_permissions, $manager_permissions));

        $employeeRole = Role::firstOrCreate(['name' => 'employee', 'guard_name' => 'sanctum']);

        $employeeRole->givePermissionTo($employee_permissions);
        }
}
