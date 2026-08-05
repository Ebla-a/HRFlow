<?php 

namespace Modules\Payroll\App\DTOs;

final readonly class PayrollRunDTO
{
    public function __construct(
        public int $month,
        public int $year,
        public ?string $notes = null,
    ) {}
}