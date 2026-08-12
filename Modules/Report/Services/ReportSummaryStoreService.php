<?php

declare(strict_types=1);

namespace Modules\Report\Services;

use Illuminate\Support\Facades\Cache;
use Modules\Report\Entities\ReportSummary;

final class ReportSummaryStoreService
{
    /**
     * Store a report summary both in the table and in the cache.
     *
     * @param string $reportType payroll|attendance|leave|performance|employees
     * @param int|null $month
     * @param int|null $year
     * @param array<string, mixed> $data
     */
    public function store(
        string $reportType,
        ?int $month,
        ?int $year,
        array $data,
        int $cacheTtlHours = 12
    ): ReportSummary {
        $report = ReportSummary::updateOrCreate(
            [
                'report_type' => $reportType,
                'month' => $month,
                'year' => $year,
            ],
            [
                'data' => $data,
                'generated_at' => now(),
            ]
        );

        $cacheKey = $this->cacheKey($reportType, $month, $year);

        Cache::put($cacheKey, $report->data, now()->addHours($cacheTtlHours));

        return $report;
    }

    /**
     * Read a report summary from cache first, then from the table.
     *
     * @return array<string, mixed>|null
     */
    public function read(string $reportType, ?int $month, ?int $year): ?array
    {
        $cacheKey = $this->cacheKey($reportType, $month, $year);

        $cached = Cache::get($cacheKey);

        if ($cached !== null) {
            return $cached;
        }

        $report = ReportSummary::query()
            ->where('report_type', $reportType)
            ->where('month', $month)
            ->where('year', $year)
            ->first();

        if ($report === null) {
            return null;
        }

        Cache::put($cacheKey, $report->data, now()->addHours(12));

        return $report->data;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function list(string $reportType, ?int $year = null): array
    {
        return ReportSummary::query()
            ->where('report_type', $reportType)
            ->when($year, fn ($q) => $q->where('year', $year))
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->get()
            ->map(fn (ReportSummary $report) => $report->data)
            ->all();
    }

    private function cacheKey(string $reportType, ?int $month, ?int $year): string
    {
        return "report_summary_{$reportType}_{$month}_{$year}";
    }
}
