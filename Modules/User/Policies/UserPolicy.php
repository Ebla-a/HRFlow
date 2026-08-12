<?php

declare(strict_types=1);

namespace Modules\User\Policies;

use Modules\User\Entities\User;

class UserPolicy
{
    /**
     * Determine whether the authenticated user
     * can view the users list.
     */
    public function viewAny(User $authUser): bool
    {
        return $authUser->hasAnyPermission(
            [
                'view.users.all',
                'view.department.employees.own',
                'employee.view.own.profile',
            ],
            'sanctum'
        );
    }

    /**
     * Determine whether the authenticated user
     * can view a specific user.
     */
    public function view(
        User $authUser,
        User $targetUser
    ): bool {
        /*
         * HR Admin can view all users.
         */
        if ($authUser->hasPermissionTo(
            'view.users.all',
            'sanctum'
        )) {
            return true;
        }

        /*
         * Manager can view employees
         * belonging to the same department.
         */
        if ($authUser->hasPermissionTo(
            'view.department.employees.own',
            'sanctum'
        )) {
            return $this->sameDepartment(
                $authUser,
                $targetUser
            );
        }

        /*
         * Employee can view his own profile.
         */
        if ($authUser->hasPermissionTo(
            'employee.view.own.profile',
            'sanctum'
        )) {
            return $authUser->id === $targetUser->id;
        }

        return false;
    }

    /**
     * Determine whether the authenticated user
     * can update another user's email.
     */
    public function updateEmail(
        User $authUser,
        User $targetUser
    ): bool {
        /*
         * HR Admin / authorized user.
         */
        if ($authUser->hasPermissionTo(
            'update.user',
            'sanctum'
        )) {
            return true;
        }

        /*
         * Manager can update users
         * in the same department.
         */
        if ($authUser->hasPermissionTo(
            'view.department.employees.own',
            'sanctum'
        )) {
            return $this->sameDepartment(
                $authUser,
                $targetUser
            );
        }

        return false;
    }

    /**
     * Determine whether the authenticated user
     * can update another user's avatar.
     */
    public function updateAvatar(
        User $authUser,
        User $targetUser
    ): bool {
        /*
         * HR Admin / users with global user management.
         */
        if ($authUser->hasPermissionTo(
            'users.manage.all',
            'sanctum'
        )) {
            return true;
        }

        /*
         * Users with general update permission.
         */
        if ($authUser->hasPermissionTo(
            'update.user',
            'sanctum'
        )) {
            return true;
        }

        /*
         * A user can always update his own avatar.
         */
        if ($authUser->id === $targetUser->id) {
            return true;
        }

        /*
         * Manager can update users
         * in the same department.
         */
        if ($authUser->hasPermissionTo(
            'view.department.employees.own',
            'sanctum'
        )) {
            return $this->sameDepartment(
                $authUser,
                $targetUser
            );
        }

        return false;
    }

    /**
     * Determine whether the authenticated user
     * can activate a user account.
     */
    public function activate(
        User $authUser,
        User $targetUser
    ): bool {
        return $authUser->hasPermissionTo(
            'users.manage.all',
            'sanctum'
        );
    }

    /**
     * Determine whether the authenticated user
     * can deactivate a user account.
     */
    public function deactivate(
        User $authUser,
        User $targetUser
    ): bool {
        return $authUser->hasPermissionTo(
            'users.manage.all',
            'sanctum'
        );
    }

    /**
     * Determine whether the authenticated user
     * can manage roles.
     */
    public function manageRoles(
        User $authUser
    ): bool {
        return $authUser->hasPermissionTo(
            'roles.manage',
            'sanctum'
        );
    }

    /**
     * Determine whether the authenticated user
     * can manage permissions.
     */
    public function managePermissions(
        User $authUser
    ): bool {
        return $authUser->hasPermissionTo(
            'permissions.manage',
            'sanctum'
        );
    }

    /**
     * Check whether two users belong to the same department.
     */
    private function sameDepartment(
        User $authUser,
        User $targetUser
    ): bool {
        $authDepartmentId = $authUser->employee?->department_id;
        $targetDepartmentId = $targetUser->employee?->department_id;

        return $authDepartmentId !== null
            && $authDepartmentId === $targetDepartmentId;
    }
}