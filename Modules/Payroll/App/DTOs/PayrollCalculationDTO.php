<?php

namespace Modules\Payroll\App\DTOs;

final readonly class PayrollCalculationDTO
{
    public function __construct(
        public float $basicSalary,
        public float $housingAllowance,
        public float $transportAllowance,
        public float $otherAllowance,
        public float $grossSalary,
        public float $unpaidLeaveDeduction,
        public int $unpaidLeaveDays,
        public float $netSalary,
    ) {}
}