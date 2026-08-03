<?php

declare(strict_types=1);

namespace Modules\Payroll\Entities;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Modules\Payroll\App\Enums\PayrollRunStatus;

#[Fillable([  'month',
        'year',
        'status',
        'processed_at',
        'processed_by',
        'finalized_at',
        'finalized_by',
        ])]
final class PayrollRun extends Model
{
    protected $table = 'payroll_runs';

    /**
     * @return array{processed_at: string, status: string}
     */
    protected function casts(): array
    {
        return [
        'status' => PayrollRunStatus::class,
        'processed_at' => 'datetime',
        'finalized_at' => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    /**
     * @return BelongsTo<User, PayrollRun>
     */
    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }


    /**
     * @return BelongsTo<User, PayrollRun>
     */
    public function finalizedBy(): BelongsTo
    {
    return $this->belongsTo(User::class, 'finalized_by');
     }

    /**
     * @return HasMany<Payslip, PayrollRun>
     */
    public function payslips(): HasMany
    {
        return $this->hasMany(Payslip::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */
    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status', PayrollRunStatus::Draft);
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeProcessing(Builder $query): Builder
    {
        return $query->where('status', PayrollRunStatus::Processing);
    }


    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeFinalized(Builder $query): Builder
    {
        return $query->where('status', PayrollRunStatus::Finalized);
    }

    /**
     * @param Builder $query
     * @param int $month
     * @param int $year
     * @return Builder
     */
    public function scopeForMonth(
        Builder $query,
        int $month,
        int $year
    ): Builder {
        return $query
            ->where('month', $month)
            ->where('year', $year);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * @return bool
     */
    public function isDraft(): bool
    {
        return $this->status === PayrollRunStatus::Draft;
    }

    /**
     * @return bool
     */
    public function isProcessing(): bool
    {
        return $this->status === PayrollRunStatus::Processing;
    }

    /**
     * @return bool
     */
    public function isFinalized(): bool
    {
        return $this->status === PayrollRunStatus::Finalized;
    }

    /**
     * @param int $userId
     * @return void
     */
    public function markAsProcessing(int $userId): void
    {
        $this->update([
            'status' => PayrollRunStatus::Processing,
            'processed_at' => now(),
            'processed_by' => $userId,
        ]);
    }

    /**
     * @return void
     */
    public function markAsFinalized(): void
    {
        $this->update([
            'status' => PayrollRunStatus::Finalized,
        ]);
    }
}