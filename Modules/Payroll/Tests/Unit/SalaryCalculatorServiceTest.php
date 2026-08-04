<?php

namespace Modules\Payroll\Tests\Unit;

use Tests\TestCase;
use Modules\Payroll\Entities\SalaryStructure;
use Modules\Payroll\Services\SalaryCalculatorService;

class SalaryCalculatorServiceTest extends TestCase
{
    public function test_it_calculates_gross_and_net_salary_without_deductions(): void
    {
        $structure = new SalaryStructure([
            'basic_salary' => 5000.00,
            'housing_allowance' => 1000.00,
            'transport_allowance' => 500.00,
            'other_allowance' => 200.00,
        ]);

        $service = new SalaryCalculatorService();
        $result = $service->calculate($structure);

        $this->assertEquals(5000.00, $result->basicSalary);
        $this->assertEquals(1000.00, $result->housingAllowance);
        $this->assertEquals(500.00, $result->transportAllowance);
        $this->assertEquals(200.00, $result->otherAllowance);
        $this->assertEquals(6700.00, $result->grossSalary);
        $this->assertEquals(0.00, $result->unpaidLeaveDeduction);
        $this->assertEquals(6700.00, $result->netSalary);
    }

    public function test_it_calculates_unpaid_leave_deduction_correctly(): void
    {
        $structure = new SalaryStructure([
            'basic_salary' => 3000.00,
            'housing_allowance' => 500.00,
            'transport_allowance' => 0.00,
            'other_allowance' => 0.00,
        ]);

        $service = new SalaryCalculatorService();
        $result = $service->calculate($structure, 3);

        $this->assertEquals(3500.00, $result->grossSalary);
        $this->assertEquals(300.00, $result->unpaidLeaveDeduction);
        $this->assertEquals(3200.00, $result->netSalary);
    }

    public function test_it_prevents_net_salary_from_going_negative(): void
    {
        $structure = new SalaryStructure([
            'basic_salary' => 1000.00,
            'housing_allowance' => 0.00,
            'transport_allowance' => 0.00,
            'other_allowance' => 0.00,
        ]);

        $service = new SalaryCalculatorService();
        $result = $service->calculate($structure, 0, 2000.00);

        $this->assertEquals(1000.00, $result->grossSalary);
        $this->assertEquals(0.00, $result->netSalary);
    }
}