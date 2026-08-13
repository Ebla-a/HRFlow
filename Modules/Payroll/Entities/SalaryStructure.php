<?php

declare(strict_types=1);

namespace Modules\Payroll\Entities;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Employee\Entities\Employee;
use Modules\Payroll\Database\Factories\SalaryStructureFactory;

#[Fillable([
    'employee_id',
    'basic_salary',
    'housing_allowance',
    'transport_allowance',
    'other_allowance',
    'currency',
    'effective_date',
])]
final class SalaryStructure extends Model
{
    use HasFactory;
    protected $table = 'salary_structures';

    protected static function newFactory(): SalaryStructureFactory
    {
        return SalaryStructureFactory::new();
    }

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

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function scopeForEmployee(
        Builder $query,
        int $employeeId
    ): Builder {
        return $query->where('employee_id', $employeeId);
    }
}