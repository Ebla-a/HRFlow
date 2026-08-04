<?php

declare(strict_types=1);

namespace Modules\Payroll\Entities;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Modules\Employee\Entities\Employee;

#[Fillable([
    'employee_id',
    'reason',
    'effective_date',
    'changed_by',
])]
final class SalaryHistory extends Model
{
    protected $table = 'salary_histories';

    protected function casts(): array
    {
        return [
            'effective_date' => 'date',
        ];
    }

    /**
     * -----------------------------------------------
     * Relations
     * ----------------------------------------------
     */

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    public function historyItems(): HasMany
    {
        return $this->hasMany(SalaryHistoryItem::class);
    }

    /**
     * --------------------------------------------
     * Scpoes
     * --------------------------------------------
     */

    public function scopeForEmployee(
        Builder $query,
        int $employeeId
    ): Builder {
        return $query->where('employee_id', $employeeId);
    }

    public function scopeLatestFirst(Builder $query): Builder
    {
        return $query->latest('effective_date');
    }
}