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
      /**
       * Summary of viewAny
       * @param User $user
       * @return bool
       */
      public function viewAny(User $user): bool
    {
        return $user->can('departments.view.all');
    }
    /**
     * Summary of view
     * @param User $user
     * @param Department $department
     * @return bool
     */
    public function view(User $user, Department $department): bool
    {
        //if user role =hr
        if ($user->can('departments.view.all')) {
            return true;
        }
//if user has permission to view own department, check if the user is the manager of the department
        return $department->manager_id && $user->employee?->id === $department->manager_id;
    }

    /**
     * Summary of delete
     * @param User $user
     * @param Department $department
     * @return bool
     */
    public function delete(User $user, Department $department): bool
    {
        if (! $user->can('department.delete')) {
            return false;
        }

        return ! $department->employees()->exists();
    }

    /**
     * Summary of create
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->can('department.create');
    }
    /**
     * Summary of update
     * @param User $user
     * @param Department $department
     * @return bool
     */
    public function update(User $user, Department $department): bool
    {
        return $user->can('department.update');
    }



}
