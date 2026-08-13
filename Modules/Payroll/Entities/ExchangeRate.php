<?php

declare(strict_types=1);

namespace Modules\Payroll\Entities;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

#[Fillable([
    'from_currency',
    'to_currency',
    'rate',
    'rate_date',
    'provider',
])]
class ExchangeRate extends Model
{
    protected $casts = [
        'rate' => 'decimal:8',
        'rate_date' => 'date',
    ];

    /**
     * Scope rates for a specific currency pair.
     */
    public function scopeForPair(
        Builder $query,
        string $fromCurrency,
        string $toCurrency,
    ): Builder {
        return $query
            ->where('from_currency', $fromCurrency)
            ->where('to_currency', $toCurrency);
    }

    /**
     * Scope rates for a specific date.
     */
    public function scopeForDate(
        Builder $query,
        string $date,
    ): Builder {
        return $query->whereDate('rate_date', $date);
    }
}