<?php

namespace Modules\Employee\App\DTOs;

use Modules\Employee\App\Http\Requests\V1\StoreEmployeeRequest;

class CreateEmployeeDTO
{
    public function __construct(
        public readonly string $email,
        public readonly string $firstName,
        public readonly string $lastName,
        public readonly int $departmentId,
        public readonly int $jobTitleId,
        public readonly string $employeeNumber,
        public readonly string $employmentType,
        public readonly string $status,
        public readonly string $hireDate,
        public readonly ?int $managerId = null,
        public readonly ?string $nationalId = null,
        public readonly ?string $phone = null,
        public readonly ?string $address = null,
        public readonly ?string $birthDate = null,
        public readonly ?string $gender = null
    ) {}

    public static function fromRequest(StoreEmployeeRequest $request): self
    {
        $validated = $request->validated();

        return new self(
            email: $validated['email'],
            firstName: $validated['first_name'],
            lastName: $validated['last_name'],
            departmentId: (int) $validated['department_id'],
            jobTitleId: (int) $validated['job_title_id'],
            employeeNumber: $validated['employee_number'],
            employmentType: $validated['employment_type'],
            status: $validated['status'],
            hireDate: $validated['hire_date'],
            managerId: isset($validated['manager_id']) ? (int) $validated['manager_id'] : null,
            nationalId: $validated['national_id'] ?? null,
            phone: $validated['phone'] ?? null,
            address: $validated['address'] ?? null,
            birthDate: $validated['birth_date'] ?? null,
            gender: $validated['gender'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'department_id' => $this->departmentId,
            'job_title_id' => $this->jobTitleId,
            'employee_number' => $this->employeeNumber,
            'employment_type' => $this->employmentType,
            'status' => $this->status,
            'hire_date' => $this->hireDate,
            'manager_id' => $this->managerId,
            'national_id' => $this->nationalId,
            'phone' => $this->phone,
            'address' => $this->address,
            'birth_date' => $this->birthDate,
            'gender' => $this->gender,
        ];
    }
}