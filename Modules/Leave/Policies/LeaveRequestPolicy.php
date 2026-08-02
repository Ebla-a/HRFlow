<?php

namespace Modules\Leave\Policies;

use App\Models\User;
use Modules\Leave\Entities\LeaveRequest;
use Modules\Leave\Enums\LeaveRequestStatusEnum;

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
    ): bool {

        // HR can view all requests
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
    ): bool {

        // only pending requests
        if ($leaveRequest->status !== LeaveRequestStatusEnum::PENDING) {
            return false;
        }

        // must be manager
        if (!$user->hasRole('Manager')) {
            return false;
        }

        if (!$user->employee) {
            return false;
        }

        // only direct manager can approve
        return $leaveRequest->employee?->manager_id
            === $user->employee->id;
    }

    /**
     * HR approval
     */
    public function approveHR(
        User $user,
        LeaveRequest $leaveRequest
    ): bool {

        // only pending requests
        if ($leaveRequest->status !== LeaveRequestStatusEnum::PENDING) {
            return false;
        }

        return $user->hasRole('Hr_admin');
    }

    /**
     * Reject request
     */
    public function reject(
        User $user,
        LeaveRequest $leaveRequest
    ): bool {

        // only pending requests
        if ($leaveRequest->status !== LeaveRequestStatusEnum::PENDING) {
            return false;
        }

        if ($user->hasRole('Hr_admin')) {
            return true;
        }

        if ($user->hasRole('Manager')) {

            return $leaveRequest->employee?->manager_id
                === $user->employee?->id;
        }

        return false;
    }
} 