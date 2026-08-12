<?php

declare(strict_types=1);

namespace Modules\Payroll\Entities;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Modules\Payroll\App\Enums\PayrollRunStatus;

#[Fillable([
    'month',
    'year',
    'status',
    'processed_at',
    'processed_by',
    'finalized_at',
    'finalized_by',
])]
final class PayrollRun extends Model
{
    use HasFactory;


    protected $table = 'payroll_runs';


    protected function casts(): array
    {
        return [
            'status' => PayrollRunStatus::class,
            'processed_at' => 'datetime',
            'finalized_at' => 'datetime',
        ];
    }
/**
 * -----------------------------------------
 * Relations
 * -----------------------------------------
 */


    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function finalizedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'finalized_by');
    }

    public function payslips(): HasMany
    {
        return $this->hasMany(Payslip::class);
    }

/**
 * --------------------------------------
 * Scpoes
 * --------------------------------------
 */

    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status', PayrollRunStatus::Draft);
    }

    public function scopeProcessing(Builder $query): Builder
    {
        return $query->where('status', PayrollRunStatus::Processing);
    }

    public function scopeFinalized(Builder $query): Builder
    {
        return $query->where('status', PayrollRunStatus::Finalized);
    }

    public function scopeForMonth(
        Builder $query,
        int $month,
        int $year
    ): Builder {
        return $query
            ->where('month', $month)
            ->where('year', $year);
    }


    /**
     * --------------------------------------------
     * Helpers
     * --------------------------------------------
     */

    public function isDraft(): bool
    {
        return $this->status === PayrollRunStatus::Draft;
    }

    public function isProcessing(): bool
    {
        return $this->status === PayrollRunStatus::Processing;
    }

    public function isFinalized(): bool
    {
        return $this->status === PayrollRunStatus::Finalized;
    }

    public function markAsProcessing(int $userId): void
    {
        $this->update([
            'status' => PayrollRunStatus::Processing,
            'processed_at' => now(),
            'processed_by' => $userId,
        ]);
    }

    public function markAsFinalized(int $userId): void
    {
        $this->update([
            'status' => PayrollRunStatus::Finalized,
            'finalized_at' => now(),
            'finalized_by' => $userId,
        ]);
    }
}