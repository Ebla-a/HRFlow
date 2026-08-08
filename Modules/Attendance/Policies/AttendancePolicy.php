<?php

namespace Modules\Attendance\Policies;

use Modules\User\Entities\User;
use Modules\Attendance\Entities\Attendance;
use Illuminate\Auth\Access\HandlesAuthorization;

class AttendancePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the attendance list with filters.
     * Access: HR Admin / Manager
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyPermission([
            'attendence.view.all',
            'view.attendence.department',
        ]);
    }

    /**
     * Determine whether the user can register individual or bulk attendance.
     * Access: HR Admin ONLY
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('attendence.check.in');
    }

    /**
     * Determine whether the user can update/correct an attendance record.
     * Access: HR Admin
     */
    public function update(User $user, Attendance $attendance): bool
    {
        return $user->hasPermissionTo('attendence.correct');
    }

    /**
     * Determine whether the user can view their own personal attendance log.
     * Access: Employee
     */
    public function viewOwn(User $user): bool
    {
        return $user->hasPermissionTo('attendence.view.own');
    }

    /**
     * Determine whether the user can view the monthly department attendance summary.
     * Access: Manager / HR Admin
     */
    public function viewSummary(User $user): bool
    {
        return $user->hasAnyPermission([
            'view.attendence.department',
            'attendence.view.all',
        ]);
    }
}