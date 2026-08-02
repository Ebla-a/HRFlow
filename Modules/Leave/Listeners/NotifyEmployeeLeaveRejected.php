<?php

namespace Modules\Leave\Listeners;

use Modules\Leave\Events\LeaveRequestRejected;
use Modules\Leave\Notifications\LeaveRejectedNotification;

class NotifyEmployeeLeaveRejected
{
    public function handle(
        LeaveRequestRejected $event
    ): void
    {
        $event->leaveRequest
            ->employee
            ->user
            ->notify(
                new LeaveRejectedNotification(
                    $event->leaveRequest
                )
            );
    }
}
 