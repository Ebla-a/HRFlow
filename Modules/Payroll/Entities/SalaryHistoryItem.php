<?php

declare(strict_types=1);

namespace Modules\Payroll\Entities;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Payroll\App\Enums\SalaryField;

#[Fillable([
    'salary_history_id',
    'field',
    'old_amount',
    'new_amount',
])]
final class SalaryHistoryItem extends Model
{
    protected $table = 'salary_history_items';

    protected function casts(): array
    {
        return [
            'field' => SalaryField::class,
            'old_amount' => 'decimal:2',
            'new_amount' => 'decimal:2',
        ];
    }

    public function salaryHistory(): BelongsTo
    {
        return $this->belongsTo(SalaryHistory::class);
    }
}