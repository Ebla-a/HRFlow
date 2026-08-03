<?php

declare(strict_types=1);

namespace Modules\Payroll\Entities;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Payroll\App\Enums\PayslipDeductionType;



#[Fillable(['payslip_id',
        'type',
        'amount',
        'description',])]
final class PayslipDeduction extends Model
{
    protected $table = 'payslip_deductions';


    /**
     * @return array{amount: string, type: string}
     */
    protected function casts(): array
    {
        return [
            'type' => PayslipDeductionType::class,
            'amount' => 'decimal:2',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    /**
     * @return BelongsTo<Payslip, PayslipDeduction>
     */
    public function payslip(): BelongsTo
    {
        return $this->belongsTo(Payslip::class);
    }
}