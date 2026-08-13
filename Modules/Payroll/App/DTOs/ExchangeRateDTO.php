<?php

declare(strict_types=1);

namespace Modules\Payroll\App\DTOs;

use Carbon\CarbonImmutable;

final readonly class ExchangeRateDTO
{
    public function __construct(
        public string $fromCurrency,
        public string $toCurrency,
        public float $rate,
        public CarbonImmutable $date,
        public string $provider,
    ) {}
}