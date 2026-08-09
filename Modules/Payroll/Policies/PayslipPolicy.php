<?php

namespace Modules\Payroll\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\User\Entities\User;
use Modules\Payroll\Entities\Payslip;

class PayslipPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        if ($user->hasPermissionTo('view.payslip.all', 'sanctum')) {
        return true;
    }

  
    if ($user->hasPermissionTo('view.payslip.own', 'sanctum')) {
        return true;
    }

    return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Payslip $payslip): bool
    {

    if ($user->hasPermissionTo('view.payslip.all', 'sanctum')) {
        return true;
    }

    if ($user->hasPermissionTo('view.payslip.own', 'sanctum') && $payslip->employee_id === $user->employee?->id) {
        return true;
    }

    return false;
}
    

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('generate.payslip');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Payslip $payslip): bool
    {
        return $user->hasPermissionTo('update.payslip');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Payslip $payslip): bool
    {
        return $user->hasPermissionTo('delete.payslip');
    }
}