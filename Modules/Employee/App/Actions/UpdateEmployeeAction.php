<?php

namespace Modules\Employee\App\Actions;

use Illuminate\Support\Facades\DB;
use Modules\Employee\App\DTOs\UpdateEmployeeDTO;

use Modules\Employee\App\Exceptions\InvalidJobTitleForDepartmentException;
use Modules\Department\Entities\JobTitle;
use Modules\Employee\App\Events\EmployeeUpdated;
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
                throw  new InvalidJobTitleForDepartmentException;
            }
        }

        return DB::transaction(function () use ($employee, $data) {


            if ($employee->user) {
                $userData = [];

                if (isset($data['first_name']) || isset($data['last_name'])) {
                    $firstName = $data['first_name'] ?? $employee->first_name;
                    $lastName = $data['last_name'] ?? $employee->last_name;
                    $userData['name'] = trim("{$firstName} {$lastName}");
                }

                if (isset($data['email'])) {
                    $userData['email'] = $data['email'];
                }

                if (!empty($userData)) {
                    $employee->user->update($userData);
                }
            }

            $employee->update($data);
            event(new EmployeeUpdated($employee));

            return $employee->fresh(['user', 'department', 'jobTitle', 'manager']);
        });
    }
}
