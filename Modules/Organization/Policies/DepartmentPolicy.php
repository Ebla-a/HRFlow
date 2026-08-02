<?php

namespace Modules\Organization\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Organization\Entities\Department;
use Modules\User\Entities\User;

class DepartmentPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

      public function viewAny(User $user): bool
    {
        return $user->can('departments.view.all');
    }
    public function view(User $user, Department $department): bool
    {
        //if user role =hr
        if ($user->can('departments.view.all')) {
            return true;
        }
//if user has permission to view own department, check if the user is the manager of the department
        return $department->manager_id && $user->employee?->id === $department->manager_id;
    }


    public function delete(User $user, Department $department): bool
    {
        if (! $user->can('department.delete')) {
            return false;
        }

        return ! $department->employees()->exists();
    }


    public function create(User $user): bool
    {
        return $user->can('department.create');
    }

    public function update(User $user, Department $department): bool
    {
        return $user->can('department.update');
    }

    

}
