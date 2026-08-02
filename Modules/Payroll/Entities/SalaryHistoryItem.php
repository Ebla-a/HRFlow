<?php

namespace Modules\Payroll\Entities;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Payroll\App\Enums\SalaryField;

#[Fillable(['salary_history_id',
            'field',
            'old_amount',
            'new_amount',])]
class SalaryHistoryItem extends Model
{
    use HasFactory;

   
    /**
     * @return array{field: string, new_amount: string, old_amount: string}
     */
    protected function casts(): array
    {
        return [
            'field' => SalaryField::class,
            'old_amount' => 'decimal:2',
            'new_amount' => 'decimal:2',
        ];
    }
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<SalaryHistory, SalaryHistoryItem>
     */
    public function salaryHistory():BelongsTo
    {
        return $this->belongsTo(SalaryHistory::class);
    }
}