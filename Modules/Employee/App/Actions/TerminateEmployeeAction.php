<?php

namespace Modules\Employee\App\Actions;

use Modules\Employee\App\DTOs\TerminateEmployeeDTO;
use Modules\Employee\App\Enums\EmployeeStatus;
use Modules\Employee\Events\EmployeeTerminated;
use Illuminate\Support\Facades\DB;
use Modules\Employee\Entities\Employee;

class TerminateEmployeeAction
{
    /**
     * @param Employee $employee
     * @param TerminateEmployeeDTO $dto
     * @return Employee
     */
    public function execute(Employee $employee, TerminateEmployeeDTO $dto): Employee
    {
        return DB::transaction(function () use ($employee, $dto) {
            $employee->update([
                'status' => EmployeeStatus::TERMINATED->value,
                'termination_date' => $dto->terminationDate ?? now(),
                'termination_reason' => $dto->reason,
            ]);

            if ($employee->user) {
                $employee->user->update(['is_active' => false]);
            }

            event(new EmployeeTerminated($employee));

            return $employee->fresh(['user', 'department', 'jobTitle', 'manager']);
        });
    }
}