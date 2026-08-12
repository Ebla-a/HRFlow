<?php

declare(strict_types=1);

namespace Modules\Organization\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Organization\Entities\Department;
use Modules\User\Entities\User;

class DepartmentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the list of departments.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('departments.view', 'sanctum')
            || $user->hasPermissionTo('departments.view.all', 'sanctum');
    }

    /**
     * Determine whether the user can view a specific department.
     *
     */
    public function view(User $user, Department $department): bool
    {
        /*
         * ---------------------------------------------------------
         * 1. Detect whether this user is a manager.
         * ---------------------------------------------------------
         */
        $isManager = $user->roles()
            ->where('name', 'manager')
            ->exists();

        /*
         * ---------------------------------------------------------
         * 2. Managers can ONLY access their own department.
         * --------------------------------------------------------
         */
        if ($isManager) {
            $employee = $user->employee;

            if ($employee === null) {
                return false;
            }

            return (int) $department->manager_id === (int) $employee->id;
        }

      

        return $user->hasPermissionTo('departments.show', 'sanctum')
            || $user->hasPermissionTo('departments.view', 'sanctum')
            || $user->hasPermissionTo('departments.view.all', 'sanctum');
    }

    /**
     * Determine whether the user can create departments.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo(
            'departments.create',
            'sanctum'
        );
    }

    /**
     * Determine whether the user can update a department.
     */
    public function update(
        User $user,
        Department $department
    ): bool {
        return $user->hasPermissionTo(
            'departments.update',
            'sanctum'
        );
    }

    /**
     * Determine whether the user can delete a department.
     */
    public function delete(
        User $user,
        Department $department
    ): bool {
        if (! $user->hasPermissionTo(
            'departments.delete',
            'sanctum'
        )) {
            return false;
        }

        /*
         * Do not allow deleting a department that still
         * contains employees.
         */
        return ! $department->employees()->exists();
    }

    /**
     * Determine whether the user can restore a department.
     */
    public function restore(
        User $user,
        Department $department
    ): bool {
        return $user->hasPermissionTo(
            'departments.restore',
            'sanctum'
        );
    }

    /**
     * Determine whether the user can assign a manager.
     */
    public function assignManager(
        User $user,
        Department $department
    ): bool {
        return $user->hasPermissionTo(
            'departments.assign-manager',
            'sanctum'
        );
    }
}