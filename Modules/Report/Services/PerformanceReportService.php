<?php

declare(strict_types=1);

namespace Modules\Report\Services;

use Modules\Performance\Entities\performance_review;
use Modules\Performance\Enums\PerformanceReviewStatus;

final class PerformanceReportService
{
    /**
     * Build a performance report summary for a given year.
     *
     * @return array<string, mixed>
     */
    public function build(int $year): array
    {
        $reviews = performance_review::query()
            ->whereYear('reviewed_at', $year)
            ->get();

        $completed = $reviews->where('status', PerformanceReviewStatus::Reviewed->value);

        return [
            'period' => (string) $year,
            'year' => $year,
            'month' => null,
            'total_reviews' => $reviews->count(),
            'total_employees_rated' => $reviews->pluck('employee_id')->unique()->count(),
            'status_breakdown' => $reviews->groupBy('status')->map->count(),
            'score' => [
                'average' => $completed->isNotEmpty() ? round($completed->avg('score'), 2) : 0,
                'min' => $completed->isNotEmpty() ? $completed->min('score') : 0,
                'max' => $completed->isNotEmpty() ? $completed->max('score') : 0,
            ],
            'generated_at' => now()->toDateTimeString(),
        ];
    }
}
