<?php

namespace Modules\Leave\Policies;

use Modules\User\Entities\User;
use Modules\Leave\Entities\LeaveRequest;
use Modules\Leave\Enums\LeaveRequestStatusEnum;

class LeaveRequestPolicy
{
    /**
     * Determine whether the user can view any leave requests.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('leave.requests.view.all') 
            || $user->hasPermissionTo('view.leave.request.own')
            || $user->hasPermissionTo('view.leave.request.department');
    }

    /**
     * Determine whether the user can view the specific leave request.
     */
    public function view(User $user, LeaveRequest $leaveRequest): bool
    {
        if ($user->hasPermissionTo('leave.requests.view.all')) {
            return true;
        }

        if ($user->hasPermissionTo('view.leave.request.own')) {
            return $user->employee?->id === $leaveRequest->employee_id;
        }

        return $user->hasPermissionTo('view.leave.request');
    }

    /**
     * Determine whether the user can create leave requests.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create.leave.request');
    }

    /**
     * Manager approval
     */
    public function approveManager(User $user, LeaveRequest $leaveRequest): bool
    {
        if ($leaveRequest->status !== LeaveRequestStatusEnum::PENDING) {
            return false;
        }

        return $user->hasPermissionTo('leave.approve');
    }

    /**
     * HR approval
     */
    public function approveHR(User $user, LeaveRequest $leaveRequest): bool
    {
        if ($leaveRequest->status !== LeaveRequestStatusEnum::PENDING) {
            return false;
        }

        return $user->hasPermissionTo('leave.requests.view.all') || $user->hasPermissionTo('leave.approve');
    }

    /**
     * Reject request
     */
    public function reject(User $user, LeaveRequest $leaveRequest): bool
    {
        if ($leaveRequest->status !== LeaveRequestStatusEnum::PENDING) {
            return false;
        }

        return $user->hasPermissionTo('leave.reject');
    }
}