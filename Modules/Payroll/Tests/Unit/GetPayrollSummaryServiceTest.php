<?php

namespace Modules\Payroll\Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\Cache;
use Modules\Payroll\Entities\PayrollRun;
use Modules\Payroll\Services\GetPayrollSummaryService;

class GetPayrollSummaryServiceTest extends TestCase
{
    public function test_it_returns_and_caches_payroll_summary(): void
    {
        Cache::shouldReceive('remember')
            ->once()
            ->andReturn([
                'payroll_run_id' => 1,
                'period' => '2026-8',
                'status' => 'draft',
                'total_employees' => 2,
                'totals' => [
                    'basic_salary' => 10000.00,
                    'housing_allowance' => 2000.00,
                    'transport_allowance' => 1000.00,
                    'other_allowance' => 0.00,
                    'gross_salary' => 13000.00,
                    'unpaid_leave_deductions' => 0.00,
                    'manual_deductions' => 0.00,
                    'net_salary' => 13000.00,
                ],
            ]);

        $payrollRun = new PayrollRun([
            'id' => 1,
            'year' => 2026,
            'month' => 8,
            'status' => 'draft',
        ]);

        $service = new GetPayrollSummaryService();
        $summary = $service->getSummary($payrollRun);

        $this->assertEquals(1, $summary['payroll_run_id']);
        $this->assertEquals('2026-8', $summary['period']);
        $this->assertEquals(13000.00, $summary['totals']['net_salary']);
    }
}