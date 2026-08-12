<?php

declare(strict_types=1);

namespace Modules\Employee\Policies;

use Modules\Employee\Entities\Employee;
use Modules\User\Entities\User;

class EmployeePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyPermission([
            'employees.view.all',
            'view.department.employees.own',
        ], 'sanctum');
    }

    public function view(User $user, Employee $employee): bool
    {
        if ($user->hasPermissionTo('employees.view.all', 'sanctum')) {
            return true;
        }

        if ($user->hasPermissionTo('view.department.employees.own', 'sanctum')) {
            return $user->employee !== null
                && $user->employee->department_id === $employee->department_id;
        }

        if ($user->hasPermissionTo('employee.view.own.profile', 'sanctum')) {
            return $user->id === $employee->user_id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('employee.create', 'sanctum');
    }

    public function update(User $user, Employee $employee): bool
    {
        return $user->hasPermissionTo('employee.update', 'sanctum');
    }

    public function terminate(User $user, Employee $employee): bool
    {
        return $user->hasPermissionTo('employee.change.status', 'sanctum');
    }
}
