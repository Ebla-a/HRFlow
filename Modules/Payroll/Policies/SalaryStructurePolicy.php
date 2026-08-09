<?php

namespace Modules\Payroll\Policies;

use Modules\User\Entities\User;
use Modules\Payroll\Entities\SalaryStructure;
use Illuminate\Auth\Access\HandlesAuthorization;

class SalaryStructurePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view.structure.salary.all', 'sanctum');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create.structure.salary', 'sanctum');
    }

    public function update(User $user, SalaryStructure $salaryStructure): bool
    {
        return $user->hasPermissionTo('update.structure.salary', 'sanctum');
    }
}