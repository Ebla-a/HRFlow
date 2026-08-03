<?php

namespace Modules\Payroll\App\DTOs;

use Modules\Payroll\App\Http\Requests\CreateSalaryStructureRequest;

final readonly class CreateSalaryStructureDTO
{
    public function __construct(
        public int $employee_id,
        public float $basic_salary,
        public float $housing_allowance,
        public float $transport_allowance,
        public float $other_allowance,
        public string $effective_date,
    ) {
    }

    public static function fromRequest(CreateSalaryStructureRequest $request): self
    {
        return new self(
            employee_id: $request->integer('employee_id'),
            basic_salary: (float) $request->input('basic_salary'),
            housing_allowance: (float) $request->input('housing_allowance', 0),
            transport_allowance: (float) $request->input('transport_allowance', 0),
            other_allowance: (float) $request->input('other_allowance', 0),
            effective_date: $request->string('effective_date')->value(),
        );
    }
}