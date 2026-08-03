<?php

declare(strict_types=1);

namespace Modules\Payroll\Entities;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Employee\Entities\Employee;

#[Fillable([ 'employee_id',
        'basic_salary',
        'housing_allowance',
        'transport_allowance',
        'other_allowance',
        'effective_date',])]
final class SalaryStructure extends Model
{
    protected $table = 'salary_structures';

    protected function casts(): array
    {
        return [
            'basic_salary' => 'decimal:2',
            'housing_allowance' => 'decimal:2',
            'transport_allowance' => 'decimal:2',
            'other_allowance' => 'decimal:2',
            'effective_date' => 'date',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */
    /**
     * @return BelongsTo<Employee, SalaryStructure>
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

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

}