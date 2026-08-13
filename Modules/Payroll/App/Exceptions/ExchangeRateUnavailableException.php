<?php

declare(strict_types=1);

namespace Modules\Payroll\App\Exceptions;

use RuntimeException;

final class ExchangeRateUnavailableException extends RuntimeException
{
    public function __construct(
        string $fromCurrency,
        string $toCurrency,
        ?string $date = null,
    ) {
        $dateMessage = $date
            ? " for date {$date}"
            : '';

        parent::__construct(
            "Exchange rate from {$fromCurrency} to {$toCurrency}{$dateMessage} is unavailable."
        );
    }
}