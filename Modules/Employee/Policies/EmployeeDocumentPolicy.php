<?php

namespace Modules\Employee\Policies;

use App\Models\User;
use Modules\Employee\Entities\Employee;
use Modules\Employee\Entities\EmployeeDocument;

class EmployeeDocumentPolicy
{
    /**
     * Determine whether the user can view any documents of a specific employee.
     *
     * @param User $user
     * @param Employee $employee
     * @return bool
     */
    public function viewAny(User $user, Employee $employee): bool
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
     * Determine whether the user can upload documents for an employee.
     *
     * @param User $user
     * @param Employee $employee
     * @return bool
     */
    public function store(User $user, Employee $employee): bool
    {
        return $user->hasRole('HR Admin');
    }

    /**
     * Determine whether the user can update/replace a specific document.
     *
     * @param User $user
     * @param EmployeeDocument $document
     * @return bool
     */
    public function update(User $user, EmployeeDocument $document): bool
    {
        return $user->hasRole('HR Admin');
    }

    /**
     * Determine whether the user can delete a document.
     *
     * @param User $user
     * @param EmployeeDocument $document
     * @return bool
     */
    public function destroy(User $user, EmployeeDocument $document): bool
    {
        return $user->hasRole('HR Admin');
    }
}