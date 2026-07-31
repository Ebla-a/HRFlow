<?php

namespace Modules\User\Policies;

use Modules\User\Entities\User;

class UserPolicy
{
   
    /**
     * HR : see all
     * Manager: only his department users
     * Employee : see himslef only
     * @param User $authUser
     * @return bool
     */
    public function viewAny(User $authUser): bool
    {
        return $authUser->hasRole('Hr_admin')
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
        if ($authUser->hasRole('Hr_admin')) {
            return true;
        }

        // Manager sees employees in his department only
        if ($authUser->hasRole('Manager')) {
            return $authUser->department_id === $targetUser->department_id;
        }

        // Employee sees only himself
        if ($authUser->hasRole('Employee')) {
            return $authUser->id === $targetUser->id;
        }

        return false;
    }

    /**
     * emial updating
     * HR Admin: allow
     * Manager: his department users only
     * Employee: unallowed
     */
    public function updateEmail(User $authUser, User $targetUser): bool
    {
        if ($authUser->hasRole('Hr_admin')) {
            return true;
        }

        if ($authUser->hasRole('Manager')) {
            return $authUser->department_id === $targetUser->department_id;
        }

        return false;
    }

    /**
     * updating image profile
     * @param User $authUser
     * @param User $targetUser
     * @return bool
     */
    public function updateAvatar(User $authUser, User $targetUser): bool
    {
        if ($authUser->hasRole('Hr_admin')) {
            return true;
        }

        if ($authUser->hasRole('Manager')) {
            return $authUser->department_id === $targetUser->department_id;
        }

        return false;
    }

    /**
     * active & deactive account only HR
     * @param User $authUser
     * @return bool
     */
    public function activate(User $authUser): bool
    {
        return $authUser->hasRole('Hr_admin');
    }

    public function deactivate(User $authUser): bool
    {
        return $authUser->hasRole('Hr_admin');
    }
}
