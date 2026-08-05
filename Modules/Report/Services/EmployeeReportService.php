<?php

declare(strict_types=1);

namespace Modules\Report\Services;

use Modules\Employee\Entities\Employee;
use Modules\Employee\App\Enums\EmployeeStatus;

final class EmployeeReportService
{
    /**
     * Build an employee report summary for a given month/year.
     *
     * @return array<string, mixed>
     */
    public function build(int $month, int $year): array
    {
        $baseQuery = Employee::query();

        $totalEmployees = (clone $baseQuery)->count();

        $activeEmployees = (clone $baseQuery)
            ->where('status', EmployeeStatus::ACTIVE->value)
            ->count();

        $terminatedEmployees = (clone $baseQuery)
            ->where('status', EmployeeStatus::TERMINATED->value)
            ->count();

        $hires = (clone $baseQuery)
            ->whereYear('hire_date', $year)
            ->whereMonth('hire_date', $month)
            ->count();

        $terminations = (clone $baseQuery)
            ->whereYear('termination_date', $year)
            ->whereMonth('termination_date', $month)
            ->count();

        return [
            'period' => "{$year}-{$month}",
            'month' => $month,
            'year' => $year,
            'headcount' => [
                'total' => $totalEmployees,
                'active' => $activeEmployees,
                'terminated' => $terminatedEmployees,
            ],
            'movement' => [
                'hires' => $hires,
                'terminations' => $terminations,
                'net_change' => $hires - $terminations,
            ],
            'generated_at' => now()->toDateTimeString(),
        ];
    }
}
