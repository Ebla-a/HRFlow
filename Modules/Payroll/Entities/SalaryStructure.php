<?php

namespace Modules\Payroll\Entities;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Employee\Entities\Employee;


#[Fillable([ 'employee_id','basic_salary','housing_allowance','transport_allowance','other_allowance','effective_date',])]
  
class SalaryStructure extends Model
{
    use HasFactory;

    /**
     * @return array{basic_salary: string, effective_date: string, housing_allowance: string, other_allowance: string, transport_allowance: string}
     */
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
    /**
     * @return BelongsTo<Employee, SalaryStructure>
     */
    public function employee():BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
    /**
     * @param Builder $query
     * @param int $employeeId
     * @return Builder
     */
    public function scopeForEmployee(Builder $query, int $employeeId): Builder
    {
        return $query->where('employee_id', $employeeId);
    }
    /**
     * @return Attribute
     */
    protected function totalAllowances(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->housing_allowance
                + $this->transport_allowance
                + $this->other_allowance,
        );
    }
    /**
     * @return Attribute
     */
    protected function grossSalary(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->basic_salary + $this->total_allowances,
        );
    }
    /**
     * @return Attribute
     */
    protected function basicSalary(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => max(0, $value),
        );
    }
    /**
     * @return Attribute
     */
    protected function housingAllowance(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => max(0, $value),
        );
    }
    /**
     * @return Attribute
     */
    protected function transportAllowance(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => max(0, $value),
        );
    }
    /**
     * @return Attribute
     */
    protected function otherAllowance(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => max(0, $value),
        );
    }
}