<?php

declare(strict_types=1);

namespace Modules\Payroll\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Payroll\Contracts\ExchangeRateFetcherInterface;
use Modules\Payroll\Entities\ExchangeRate;
use Illuminate\Support\Facades\Log;
use Throwable;

final class FetchLatestExchangeRatesJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public int $backoff = 60;

    public function __construct(
        private readonly string $baseCurrency = 'USD',
    ) {}

    public function handle(
        ExchangeRateFetcherInterface $fetcher,
    ): void {
        $rates = $fetcher->fetchLatestRates(
            $this->baseCurrency
        );

        $rateDate = now()->toDateString();

        foreach ($rates as $currency => $rate) {
            if ($currency === $this->baseCurrency) {
                continue;
            }

            ExchangeRate::updateOrCreate(
                [
                    'from_currency' => $this->baseCurrency,
                    'to_currency' => $currency,
                    'rate_date' => $rateDate,
                    'provider' => config(
                        'services.exchange_rate.provider',
                        'exchange-rate-api'
                    ),
                ],
                [
                    'rate' => $rate,
                ]
            );
        }
    }

    public function failed(Throwable $exception): void
    {
        Log::critical('Exchange Rate API failed after 3 retries.', [
        'error' => $exception->getMessage(),
        'base_currency' => $this->baseCurrency,
    ]);

    report($exception);
    }
}