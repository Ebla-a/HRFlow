<?php

namespace Modules\Organization\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Organization\Entities\Department;
use Modules\User\Entities\User;

class DepartmentPolicy
{
    use HandlesAuthorization;

    public function __construct()
    {
        //
    }

    public function viewAny(User $user): bool
    {
        return $user->can('departments.view') || $user->can('departments.view.all');
    }

    public function view(User $user, Department $department): bool
    {
        
        if ($user->can('departments.view') || $user->can('departments.view.all')) {
            return true;
        }

        return $department->manager_id && $user->employee?->id === $department->manager_id;
    }

    public function create(User $user): bool
    {
        return $user->can('departments.create');
    }

    public function update(User $user, Department $department): bool
    {
        
        return $user->can('departments.update');
    }

    public function delete(User $user, Department $department): bool
    {
       
        if (! $user->can('departments.delete')) {
            return false;
        }

        return ! $department->employees()->exists();
    }
}