<?php

namespace Modules\Employee\App\Observers;

use Modules\Employee\App\Entities\Employee;
use Modules\Employee\Entities\Employee as EntitiesEmployee;

class EmployeeObserver
{
    public function created(EntitiesEmployee $employee): void
    {
         // Logic after employee creation will be added here
        // Example:
        // Create default salary structure
        // Create employee related records
    }
}