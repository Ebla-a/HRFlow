<?php

namespace Modules\Employee\Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

use Modules\Employee\App\Enums\EmployeeStatus;
use Carbon\Carbon;
use Modules\Employee\Entities\Employee;

class EmployeeModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_computes_full_name_attribute_correctly(): void
    {
        $employee = new Employee([
            'first_name' => 'Ebla',
            'last_name' => 'Ali',
        ]);

        $this->assertEquals('Ebla Ali', $employee->full_name);
    }

    public function test_it_calculates_employee_age_correctly(): void
    {
        $birthDate = Carbon::now()->subYears(23)->format('Y-m-d');
        
        $employee = new Employee([
            'birth_date' => $birthDate,
        ]);

        $this->assertEquals(23, $employee->age);
    }

    public function test_it_calculates_years_of_service_correctly(): void
    {
        $hireDate = Carbon::now()->subYears(3)->format('Y-m-d');

        $employee = new Employee([
            'hire_date' => $hireDate,
        ]);

        $this->assertEquals(3, $employee->years_of_service);
    }

    public function test_active_scope_filters_only_active_employees(): void
    {
        $activeEmployee = Employee::factory()->create(['status' => EmployeeStatus::ACTIVE->value]);
        $terminatedEmployee = Employee::factory()->create(['status' => EmployeeStatus::TERMINATED->value]);

        $activeEmployees = Employee::active()->get();

        $this->assertTrue($activeEmployees->contains($activeEmployee));
        $this->assertFalse($activeEmployees->contains($terminatedEmployee));
    }
}