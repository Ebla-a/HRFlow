<?php

namespace Modules\Employee\App\Events;

use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Employee\Entities\Employee;

class EmployeeUpdated implements ShouldHandleEventsAfterCommit
{
    use Dispatchable, SerializesModels;

    public function __construct(public Employee $employee) {}
}