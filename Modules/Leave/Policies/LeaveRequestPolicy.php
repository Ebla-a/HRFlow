<?php

namespace Modules\Leave\Policies;

use App\Models\User;
use Modules\Leave\Entities\LeaveRequest;

class LeaveRequestPolicy
{
    /**
     * Employee can create leave request
     */
    public function create(User $user): bool
    {
        return $user->employee !== null;
    }

    /**
     * User can view leave request
     */
    public function view(
        User $user,
        LeaveRequest $leaveRequest
    ): bool
    {
        // HR Admin can view all
        if ($user->hasRole('Hr_admin')) {
            return true;
        }

        // Employee can view his own request
        return $user->employee?->id 
            === $leaveRequest->employee_id;
    }

    /**
     * Manager approval
     */
    public function approveManager(
        User $user,
        LeaveRequest $leaveRequest
    ): bool
    {
        if (!$user->employee) {
            return false;
        }

        // must be manager
        if (!$user->hasRole('Manager')) {
            return false;
        }

        // manager can approve only his department employees
        return $leaveRequest->employee?->manager_id
               === $user->employee->id;
    }

    /**
     * HR approval
     */
    public function approveHR(
        User $user
    ): bool
    {
        return $user->hasRole('Hr_admin');
    }

    /**
     * Reject request
     */
    public function reject(
        User $user,
        LeaveRequest $leaveRequest
    ): bool
    {
        if ($user->hasRole('Hr_admin')) {
            return true;
        }

        if ($user->hasRole('Manager')) {

         return $leaveRequest->employee?->manager_id
              === $user->employee->id;
        }

        return false;
    }
}
 