<?php

namespace Modules\Payroll\App\DTOs;

use Modules\Payroll\App\Enums\PayslipDeductionType;

final readonly class DeductionDTO
{
    public function __construct(
        public PayslipDeductionType $type,
        public float $amount,
        public ?string $description = null,
    ) {}
}