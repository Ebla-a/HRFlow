<?php
declare(strict_types=1);

namespace Modules\Payroll\App\Actions\PayrollRun;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Modules\Employee\Entities\Employee;
use Modules\Payroll\App\Exceptions\SalaryStructureNotFoundException;
use Modules\Payroll\Services\CurrencyConversionService as ServicesCurrencyConversionService;
use Modules\Payroll\Contracts\ExchangeRateProviderInterface;
use Modules\Payroll\Entities\PayrollRun;
use Modules\Payroll\Entities\Payslip;
use Modules\Payroll\Services\SalaryCalculatorService;

final readonly class ProcessPayrollRunAction
{
    /**
     * Create a new action instance.
     */
    public function __construct(
        private SalaryCalculatorService $calculatorService,
        private ExchangeRateProviderInterface $exchangeRateProvider,
        private ServicesCurrencyConversionService $currencyConversionService,
    ) {}

    /**
     * Execute the payroll run processing for all active employees.
     *
     * @throws SalaryStructureNotFoundException
     */
    public function execute(PayrollRun $payrollRun, int $processedBy): PayrollRun
    {
        return DB::transaction(function () use ($payrollRun, $processedBy) {
            
            // 1. Define the carbon immutable date for the payroll run month
            $payrollDate = CarbonImmutable::create(
                year: $payrollRun->year,
                month: $payrollRun->month,
                day: 1,
            );

            // 2. Resolve base currency and target run currency
            $baseCurrency = config('services.payroll.base_currency', 'USD');
            $runCurrency = $payrollRun->currency ?? $baseCurrency;

            // 3. Fetch exchange rate snapshot for the entire run
            $exchangeRate = $this->exchangeRateProvider->getRate(
                fromCurrency: $baseCurrency,
                toCurrency: $runCurrency,
                date: $payrollDate,
            );

            // 4. Lock the exchange-rate snapshot on the PayrollRun entity
            $payrollRun->update([
                'exchange_rate' => $exchangeRate->rate,
                'exchange_rate_date' => $exchangeRate->date,
                'exchange_rate_provider' => $exchangeRate->provider,
            ]);

            // Clear any previously generated payslips for this run
            $payrollRun->payslips()->delete();

            // 5. Process active employees in chunks to optimize memory and performance
            Employee::query()
                ->active()
                ->with(['salaryStructure', 'leaveRequests' => function ($query) use ($payrollRun) {
                    $query->approved()
                        ->unpaid()
                        ->forMonth($payrollRun->month, $payrollRun->year);
                }])
                ->chunkById(200, function ($employees) use ($payrollRun, $exchangeRate, $runCurrency) {
                    $now = now();
                    $payslipsToInsert = [];

                    foreach ($employees as $employee) {
                        if (! $employee->salaryStructure) {
                            throw new SalaryStructureNotFoundException((string) $employee->id);
                        }

                        $unpaidDays = (int) $employee->leaveRequests->sum('days_count');

                        // Calculate salary details based on structure and unpaid leaves
                        $calculation = $this->calculatorService->calculate(
                            salaryStructure: $employee->salaryStructure,
                            unpaidLeaveDays: $unpaidDays,
                            manualDeductions: 0
                        );

                        // Convert calculated amounts to the target payroll run currency
                        $basicSalary = $this->currencyConversionService->convert($calculation->basicSalary, $exchangeRate);
                        $housingAllowance = $this->currencyConversionService->convert($calculation->housingAllowance, $exchangeRate);
                        $transportAllowance = $this->currencyConversionService->convert($calculation->transportAllowance, $exchangeRate);
                        $otherAllowance = $this->currencyConversionService->convert($calculation->otherAllowance, $exchangeRate);
                        $grossSalary = $this->currencyConversionService->convert($calculation->grossSalary, $exchangeRate);
                        $unpaidLeaveDeduction = $this->currencyConversionService->convert($calculation->unpaidLeaveDeduction, $exchangeRate);
                        $netSalary = $this->currencyConversionService->convert($calculation->netSalary, $exchangeRate);

                        // Prepare data for bulk database insertion (High Performance)
                        $payslipsToInsert[] = [
                            'payroll_run_id' => $payrollRun->id,
                            'employee_id' => $employee->id,
                            'currency' => $runCurrency,
                            'basic_salary' => $basicSalary,
                            'housing_allowance' => $housingAllowance,
                            'transport_allowance' => $transportAllowance,
                            'other_allowance' => $otherAllowance,
                            'gross_salary' => $grossSalary,
                            'deductions' => 0,
                            'unpaid_leave_deduction' => $unpaidLeaveDeduction,
                            'unpaid_leave_days' => $calculation->unpaidLeaveDays,
                            'net_salary' => $netSalary,
                            'exchange_rate_used' => $exchangeRate->rate,
                            'exchange_rate_date' => $exchangeRate->date,
                            'exchange_rate_provider' => $exchangeRate->provider,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }

                    // Perform bulk insert for better database performance
                    if (! empty($payslipsToInsert)) {
                        Payslip::insert($payslipsToInsert);
                    }
                });

            // 6. Mark payroll run status as processing
            $payrollRun->markAsProcessing($processedBy);

            return $payrollRun->fresh(['payslips']);
        });
    }
}