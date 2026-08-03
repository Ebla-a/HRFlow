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

#[Fillable([ 'employee_id',
        'reason',
        'effective_date',
        'changed_by',])]
final class SalaryHistory extends Model
{
    protected $table = 'salary_histories';

    /**
     * @return array{effective_date: string}
     */
    protected function casts(): array
    {
        return [
            'effective_date' => 'date',
        ];
    }

   /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    /**
     * @return BelongsTo<Employee, SalaryHistory>
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }


    /**
     * @return BelongsTo<User, SalaryHistory>
     */
    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }


    /**
     * @return HasMany<SalaryHistoryItem, SalaryHistory>
     */
    public function historyItems(): HasMany
    {
        return $this->hasMany(SalaryHistoryItem::class);
    }


    /*
    |--------------------------------------------------------------------------
    | Scops
    |--------------------------------------------------------------------------
    */

    /**
     * @param Builder $query
     * @param int $employeeId
     * @return Builder
     */
    public function scopeForEmployee(
        Builder $query,
        int $employeeId
    ): Builder {
        return $query->where('employee_id', $employeeId);
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeLatestFirst(Builder $query): Builder
    {
        return $query->latest('effective_date');
    }
}