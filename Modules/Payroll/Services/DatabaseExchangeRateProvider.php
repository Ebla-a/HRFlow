<?php

declare(strict_types=1);

namespace Modules\Payroll\Services;

use Carbon\CarbonImmutable;
use Modules\Payroll\App\DTOs\ExchangeRateDTO;
use Modules\Payroll\App\Exceptions\ExchangeRateUnavailableException;
use Modules\Payroll\Contracts\ExchangeRateProviderInterface;
use Modules\Payroll\Entities\ExchangeRate;

final class DatabaseExchangeRateProvider implements ExchangeRateProviderInterface
{
    public function getRate(
        string $fromCurrency,
        string $toCurrency,
        CarbonImmutable $date,
    ): ExchangeRateDTO {
        if ($fromCurrency === $toCurrency) {
            return new ExchangeRateDTO(
                fromCurrency: $fromCurrency,
                toCurrency: $toCurrency,
                rate: 1.0,
                date: $date,
                provider: 'internal',
            );
        }

        $rateRecord = ExchangeRate::query()
            ->where('from_currency', $fromCurrency)
            ->where('to_currency', $toCurrency)
            ->whereDate('rate_date', $date->toDateString())
            ->first();

        if (!$rateRecord) {
            throw new ExchangeRateUnavailableException(
                fromCurrency: $fromCurrency,
                toCurrency: $toCurrency,
                date: $date->toDateString(),
            );
        }

        return new ExchangeRateDTO(
            fromCurrency: $rateRecord->from_currency,
            toCurrency: $rateRecord->to_currency,
            rate: (float) $rateRecord->rate,
            date: CarbonImmutable::parse($rateRecord->rate_date),
            provider: $rateRecord->provider,
        );
    }
}