<?php

declare(strict_types=1);

namespace Modules\Payroll\App\Services;

use Modules\Payroll\App\DTOs\ExchangeRateDTO;

final class CurrencyConversionService
{
    /**
     * Convert an amount using an already resolved exchange rate.
     *
     * The exchange rate is intentionally passed to the method
     * so the same rate can be reused across the entire payroll run.
     */
    public function convert(
        float $amount,
        ExchangeRateDTO $exchangeRate,
    ): float {
        return round(
            $amount * $exchangeRate->rate,
            2
        );
    }
}