<?php

namespace Modules\Payroll\Listeners;


use Modules\Employee\Events\EmployeeHired as EventsEmployeeHired;
use Modules\Payroll\Entities\SalaryStructure;

class CreateInitialSalaryStructure
{
    public function handle(EventsEmployeeHired $event): void
    {
        SalaryStructure::create([
            'employee_id' => $event->employee->id,
            'basic_salary' => 0,
            'housing_allowance' => 0,
            'transport_allowance' => 0,
            'other_allowance' => 0,
            'effective_date' => now()->toDateString(),
        ]);
    }
}