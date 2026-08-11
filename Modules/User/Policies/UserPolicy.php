<?php

namespace Modules\User\Policies;

use Modules\User\Entities\User;

class UserPolicy
{
    /**
     * Determine whether the user can view any users.
     */
    public function viewAny(User $authUser): bool
    {
      {
        return $authUser->hasAnyPermission([
            'view.users.all',
            'view.department.employees.own',
            'employee.view.own.profile'
        ], 'sanctum');
      }
    }


    /**
     * Determine whether the user can view a specific user.
     */
    public function view(User $authUser, User $targetUser): bool
    {
        if ($authUser->hasPermissionTo('view.users.all', 'sanctum')) {
            return true;
        }

        if ($authUser->hasPermissionTo('view.department.employees.own', 'sanctum')) {
            return $authUser->employee?->department_id !== null
                && $authUser->employee?->department_id === $targetUser->employee?->department_id;
        }

        if ($authUser->hasPermissionTo('user.view.own.profile', 'sanctum')) {
            return $authUser->id === $targetUser->id;
        }

        return false;
    }

    /**
     * Determine whether the user can update the target user's email.
     */
    public function updateEmail(User $authUser, User $targetUser): bool
    {
        if ($authUser->hasPermissionTo('update.user', 'sanctum')) { 
            return true;
        }

        if ($authUser->hasPermissionTo('view.department.employees.own', 'sanctum')) {
            return $authUser->employee?->department_id !== null
                && $authUser->employee?->department_id === $targetUser->employee?->department_id;
        }

        return false;
    }

    /**
     * Determine whether the user can update the target user's avatar.
     */
    public function updateAvatar(User $authUser, User $targetUser): bool
    {
        if ($authUser->hasPermissionTo('update.user', 'sanctum') || $authUser->hasPermissionTo('users.manage.all', 'sanctum')) {
            return true;
        }

        if ($authUser->id === $targetUser->id) {
            return true;
        }

        if ($authUser->hasPermissionTo('view.department.employees.own', 'sanctum')) {
            return $authUser->employee?->department_id !== null
                && $authUser->employee?->department_id === $targetUser->employee?->department_id;
        }

        return false;
    }

    /**
     * Determine whether the user can activate a user account.
     */
    public function activate(User $authUser): bool
    {
        return $authUser->hasPermissionTo('users.manage.all', 'sanctum');
    }

    /**
     * Determine whether the user can deactivate a user account.
     */
    public function deactivate(User $authUser): bool
    {
        return $authUser->hasPermissionTo('users.manage.all', 'sanctum');
    }

    /**
     * Determine whether the user can manage roles.
     */
    public function manageRoles(User $authUser): bool
    {
        return $authUser->hasPermissionTo('roles.manage', 'sanctum');
    }

    /**
     * Determine whether the user can manage permissions.
     */
    public function managePermissions(User $authUser): bool
    {
        return $authUser->hasPermissionTo('permissions.manage', 'sanctum');
    }
}