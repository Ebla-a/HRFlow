<?php
namespace Modules\Payroll\Policies;

use Modules\User\Entities\User;
use Modules\Payroll\Entities\PayrollRun;

class PayrollRunPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_payroll_runs');
    }

    public function view(User $user, PayrollRun $payrollRun): bool
    {
        return $user->can('view_payroll_runs');
    }


    public function process(User $user, PayrollRun $payrollRun): bool
    {
        return $user->can('update.payroll.run') && ! $payrollRun->isFinalized();
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create.payroll.run', 'sanctum');
    }

    public function finalize(User $user, PayrollRun $payrollRun): bool
    {
        return $user->hasPermissionTo('finalize.payroll.run', 'sanctum');
    }
}