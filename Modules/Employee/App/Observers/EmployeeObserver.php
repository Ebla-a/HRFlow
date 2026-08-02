<?php

namespace Modules\Employee\App\Observers;

use Modules\Employee\App\Entities\Employee;
use Modules\Employee\Entities\Employee as EntitiesEmployee;
use Modules\Payroll\Entities\SalaryStructure;

class EmployeeObserver
{
    public function created(EntitiesEmployee $employee): void
    {
       
    }
}