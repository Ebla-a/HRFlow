<?php

declare(strict_types=1);

namespace Modules\Report\Entities;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'report_type',
    'month',
    'year',
    'data',
    'generated_at',
])]
final class ReportSummary extends Model
{
    protected $table = 'report_summaries';

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'generated_at' => 'datetime',
        ];
    }
}
