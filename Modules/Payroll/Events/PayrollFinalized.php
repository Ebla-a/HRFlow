<?php

namespace Modules\Payroll\Events;

use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Payroll\Entities\PayrollRun;

class PayrollFinalized implements ShouldHandleEventsAfterCommit
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly PayrollRun $payrollRun
    ) {}
}