<?php

namespace Modules\Payroll\Entities;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['payslip_id', 'type', 'amount', 'description'])]
class PayslipItem extends Model
{
    use HasFactory;

    /**
     * @return array{amount: string}
     */
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'basic_salary' => 'decimal:2',
            'housing_allowance' => 'decimal:2',
            'transport_allowance' => 'decimal:2',
            'other_allowance' => 'decimal:2',
            'gross_salary' => 'decimal:2',
            'deductions' => 'decimal:2',
            'unpaid_leave_deduction' => 'decimal:2',
            'net_salary' => 'decimal:2',
        ];
    }

    /**
     * @return BelongsTo<Payslip, PayslipItem>
     */
    public function payslip(): BelongsTo
    {
        return $this->belongsTo(Payslip::class);
    }
}