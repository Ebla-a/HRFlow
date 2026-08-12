<?php

declare(strict_types=1);

namespace Modules\Payroll\Services;

final class CurrencyConversionService
{
    /**
     * Convert an amount from one currency to another based on the exchange rate.
     */
    public function convert(float $amount, string $fromCurrency, string $toCurrency): float
    {
        // Return the original amount if both currencies are identical
        if ($fromCurrency === $toCurrency) {
            return $amount;
        }

        // Fetch the exchange rate between the source and target currencies
        $exchangeRate = $this->getExchangeRate($fromCurrency, $toCurrency);

        // Return the converted amount rounded to two decimal places
        return round($amount * $exchangeRate, 2);
    }

    /**
     * Retrieve the exchange rate from a database table or an external API provider.
     */
    private function getExchangeRate(string $from, string $to): float
    {
        // This can be integrated with an exchange rates table or an external currency API
        return 1.0;
    }
}