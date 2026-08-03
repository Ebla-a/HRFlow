<?php

namespace Modules\Employee\App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Employee\Entities\Employee;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

class EmployeeTerminated implements ShouldHandleEventsAfterCommit
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Employee $employee
    ) {}
}