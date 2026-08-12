<?php

declare(strict_types=1);

namespace Modules\Payroll\Contracts;

interface ExchangeRateFetcherInterface
{
    /**
     * Fetch the latest exchange rates for a base currency.
     */
    public function fetchLatestRates(string $baseCurrency): array;
}