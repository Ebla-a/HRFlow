<?php
namespace Modules\Employee\Observers;


use Modules\Employee\Entities\Employee ;
use Modules\Payroll\Entities\SalaryStructure;

class EmployeeObserver
{
    public function created(Employee $employee): void
    {
       
    }
}