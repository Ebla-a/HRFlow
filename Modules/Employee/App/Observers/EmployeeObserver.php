<?php

namespace Modules\Employee\App\Observers;

use Modules\Employee\App\Models\Employee;
use Modules\Employee\Entities\Employee as EntitiesEmployee;

class EmployeeObserver
{
    public function created(EntitiesEmployee $employee): void
    {
        // Reserved for triggering core logic when an employee is persisted
    }
}