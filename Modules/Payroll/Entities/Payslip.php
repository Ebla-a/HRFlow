<?php

declare(strict_types=1);

namespace Modules\Payroll\Entities;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Employee\Entities\Employee;


#[Fillable([ 'payroll_run_id',
        'employee_id',
        'basic_salary',
        'housing_allowance',
        'transport_allowance',
        'other_allowance',
        'gross_salary',
        'deductions',
        'unpaid_leave_deduction',
        'unpaid_leave_days',
        'net_salary',])]

final class Payslip extends Model
{
    protected $table = 'payslips';

    protected function casts(): array
    {
        return [
            'basic_salary' => 'decimal:2',
            'allowances' => 'decimal:2',
            'gross_salary' => 'decimal:2',
            'deductions' => 'decimal:2',
            'unpaid_leave_deduction' => 'decimal:2',
            'net_salary' => 'decimal:2',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */


    /**
     * @return BelongsTo<PayrollRun, Payslip>
     */
    public function payrollRun(): BelongsTo
    {
        return $this->belongsTo(PayrollRun::class);
    }

    /**
     * @return BelongsTo<Employee, Payslip>
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * @return HasMany<PayslipDeduction, Payslip>
     */
    public function deductionsItems(): HasMany
    {
        return $this->hasMany(PayslipDeduction::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
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
     * @param int $payrollRunId
     * @return Builder
     */
    public function scopeForPayrollRun(
        Builder $query,
        int $payrollRunId
    ): Builder {
        return $query->where('payroll_run_id', $payrollRunId);
    }
}