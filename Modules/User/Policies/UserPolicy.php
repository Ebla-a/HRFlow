<?php

namespace Modules\User\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * HR Admin: see all
     * Manager: only his department users
     * Employee: see himself only
     */
    public function viewAny(User $authUser): bool
    {
        return $authUser->hasRole('HR Admin')
            || $authUser->hasRole('Manager')
            || $authUser->hasRole('Employee');
    }

    /**
     * @param User $authUser
     * @param User $targetUser
     * @return bool
     */
    public function view(User $authUser, User $targetUser): bool
    {
        // HR Admin sees everything
        if ($authUser->hasRole('HR Admin')) {
            return true;
        }

        // Manager sees employees in his department only
        if ($authUser->hasRole('Manager')) {
            return $authUser->employee?->department_id !== null
                && $authUser->employee?->department_id === $targetUser->employee?->department_id;
        }

        // Employee sees only himself
        if ($authUser->hasRole('Employee')) {
            return $authUser->id === $targetUser->id;
        }

        return false;
    }

    /**
     *  email updating
     * HR Admin: allow
     * Manager: his department users only
     * @param User $authUser
     * @param User $targetUser
     * @return bool
     */
    public function updateEmail(User $authUser, User $targetUser): bool
    {
        if ($authUser->hasRole('HR Admin')) {
            return true;
        }

        if ($authUser->hasRole('Manager')) {
            return $authUser->employee?->department_id !== null
                && $authUser->employee?->department_id === $targetUser->employee?->department_id;
        }

        return false;
    }

    /**
     *  updating avatar profile
     * @param User $authUser
     * @param User $targetUser
     * @return bool
     */
    public function updateAvatar(User $authUser, User $targetUser): bool
    {
        if ($authUser->hasRole('HR Admin')) {
            return true;
        }

        if ($authUser->id === $targetUser->id) {
            return true;
        }

        if ($authUser->hasRole('Manager')) {
            return $authUser->employee?->department_id !== null
                && $authUser->employee?->department_id === $targetUser->employee?->department_id;
        }

        return false;
    }

    /**
     * activate account only HR Admin
     * @param User $authUser
     * @return bool
     */
    public function activate(User $authUser): bool
    {
        return $authUser->hasRole('HR Admin');
    }
    /**
     * deactivate account only HR Admin
     * @param User $authUser
     * @return bool
     */
    public function deactivate(User $authUser): bool
    {
        return $authUser->hasRole('HR Admin');
    }
}
