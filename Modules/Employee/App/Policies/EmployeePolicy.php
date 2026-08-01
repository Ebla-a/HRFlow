<?php

namespace Modules\Employee\App\Policies;

use App\Models\User;
use Modules\Employee\Entities\Employee;

class EmployeePolicy
{
    /**
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['HR Admin', 'Department Manager']);
    }
    /**
     * @param User $user
     * @param Employee $employee
     * @return bool
     */
    public function view(User $user, Employee $employee): bool
    {
        if ($user->hasRole('HR Admin')) {
            return true;
        }

        if ($user->hasRole('Department Manager')) {
            return $user->employee && $user->employee->department_id === $employee->department_id;
        }

        return $user->id === $employee->user_id;
    }
    /**
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->hasRole('HR Admin');
    }
    /**
     * @param User $user
     * @param Employee $employee
     * @return bool
     */
    public function update(User $user, Employee $employee): bool
    {
        return $user->hasRole('HR Admin');
    }
    /**
     * @param User $user
     * @param Employee $employee
     * @return bool
     */
    public function terminate(User $user, Employee $employee): bool
    {
        return $user->hasRole('HR Admin');
    }

    /**
     * @param User $user
     * @param Employee $employee
     * @return bool
     */
    public function uploadDocument(User $user, Employee $employee): bool
    {
        return $user->hasRole('HR Admin');
    }
}