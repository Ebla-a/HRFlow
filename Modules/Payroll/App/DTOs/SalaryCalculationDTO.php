<?php

declare(strict_types=1);

namespace Modules\Payroll\App\DTOs;

final readonly class SalaryCalculationDTO
{
    public function __construct(
        public float $basicSalary,
        public float $housingAllowance,
        public float $transportAllowance,
        public float $otherAllowance,
        public float $grossSalary,
        public float $manualDeductions,
        public float $unpaidLeaveDeduction,
        public int $unpaidLeaveDays,
        public float $netSalary,
    ) {
    }
}