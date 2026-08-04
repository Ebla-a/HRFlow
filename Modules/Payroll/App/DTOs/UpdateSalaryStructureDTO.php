<?php

namespace Modules\Payroll\App\DTOs;

final readonly class UpdateSalaryStructureDTO
{
    public function __construct(
        public float $basic_salary,
        public float $housing_allowance = 0.0,
        public float $transport_allowance = 0.0,
        public float $other_allowance = 0.0,
        public ?string $effective_date = null,
        public ?string $reason = null,
    ) {}
}