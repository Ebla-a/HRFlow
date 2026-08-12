<?php

namespace Modules\Employee\Listeners;



use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;

use Modules\Employee\Events\EmployeeHired ;
use Modules\Employee\Events\EmployeeTerminated;
use Modules\Employee\Events\EmployeeUpdated;

class ClearEmployeeCacheListener implements ShouldQueue
{
    /**
     * @param EmployeeUpdated|EmployeeTerminated $event
     * @return void
     */
    public function handle(EmployeeHired|EmployeeUpdated|EmployeeTerminated $event): void
    {
        $employee = $event->employee;

       //Clear the cache of this specific employee's profile only
        Cache::tags(["employee_{$employee->id}"])->flush();
     
        // Clearing the cache of the lists related to this employee's department only
        if ($employee->department_id) {
            Cache::tags(["department_{$employee->department_id}"])->flush();
        }

        Cache::tags(['employees'])->flush();

    }


}