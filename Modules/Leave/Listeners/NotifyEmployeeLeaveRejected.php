<?php

namespace Modules\Leave\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Leave\Events\LeaveRequestRejected;
use Modules\Leave\Notifications\LeaveRejectedNotification;

class NotifyEmployeeLeaveRejected implements ShouldQueue
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
 