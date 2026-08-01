<?php

namespace Modules\Employee\App\Actions;

use Modules\Employee\App\DTOs\UpdateEmployeeDTO;

use Modules\Employee\App\Exceptions\InvalidJobTitleForDepartmentException;
use Modules\Department\Entities\JobTitle;
use Modules\Employee\Entities\Employee;

class UpdateEmployeeAction
{
    public function execute(Employee $employee, UpdateEmployeeDTO $dto): Employee
    {
        $data = $dto->data;

        $departmentId = $data['department_id'] ?? $employee->department_id;
        $jobTitleId = $data['job_title_id'] ?? $employee->job_title_id;

        if (isset($data['job_title_id']) || isset($data['department_id'])) {
            $jobTitle = JobTitle::findOrFail($jobTitleId);
            if ($jobTitle->department_id !== (int) $departmentId) {
                throw InvalidJobTitleForDepartmentException::make();
            }
        }

        $employee->update($data);

        return $employee->fresh(['user', 'department', 'jobTitle', 'manager']);
    }
}