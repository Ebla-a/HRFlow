<?php

namespace Modules\Employee\App\Actions;

use Modules\Employee\App\DTOs\CreateEmployeeDTO;

use Modules\Employee\App\Events\EmployeeHired;
use Modules\Employee\App\Exceptions\InvalidJobTitleForDepartmentException;
use App\Models\User;
use Modules\Department\Entities\JobTitle;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Modules\Employee\Entities\Employee;

class HireEmployeeAction
{
    public function execute(CreateEmployeeDTO $dto): Employee
    {
        $jobTitle = JobTitle::findOrFail($dto->jobTitleId);
        
        if ($jobTitle->department_id !== $dto->departmentId) {
            throw InvalidJobTitleForDepartmentException::make();
        }

        return DB::transaction(function () use ($dto) {
            $tempPassword = Str::random(10);

            $user = User::create([
                'name' => "{$dto->firstName} {$dto->lastName}",
                'email' => $dto->email,
                'password' => Hash::make($tempPassword),
                'is_active' => true,
            ]);

            $employeeData = array_merge($dto->toArray(), [
                'user_id' => $user->id,
            ]);

            $employee = Employee::create($employeeData);

            event(new EmployeeHired($employee, $tempPassword));

            return $employee->load(['user', 'department', 'jobTitle', 'manager']);
        });
    }
}