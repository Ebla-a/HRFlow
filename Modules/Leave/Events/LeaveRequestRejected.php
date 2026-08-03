<?php

namespace Modules\Leave\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Leave\Entities\LeaveRequest;

class LeaveRequestRejected
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public LeaveRequest $leaveRequest
    ) {
    }
}
 