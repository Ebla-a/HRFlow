<?php 

namespace Modules\Payroll\App\DTOs;

final readonly class CreateSalaryStructureDTO
{
    public function __construct(
        public int $employee_id,
        public float $basic_salary,
        public float $housing_allowance = 0.0,
        public float $transport_allowance = 0.0,
        public float $other_allowance = 0.0,
        public ?string $effective_date = null,
    ) {}
}