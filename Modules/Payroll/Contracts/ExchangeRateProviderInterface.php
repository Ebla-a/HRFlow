<?php

declare(strict_types=1);

namespace Modules\Payroll\Contracts;

use Carbon\CarbonImmutable;
use Modules\Payroll\App\DTOs\ExchangeRateDTO;

interface ExchangeRateProviderInterface
{
    /**
     * Get an exchange rate for a specific date.
     *
     * The provider is responsible only for reading
     * an already available exchange rate.
     */
    public function getRate(
        string $fromCurrency,
        string $toCurrency,
        CarbonImmutable $date,
    ): ExchangeRateDTO;
}