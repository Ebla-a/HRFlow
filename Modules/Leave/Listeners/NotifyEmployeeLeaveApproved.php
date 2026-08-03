<?php

namespace Modules\Leave\Listeners;

use Modules\Leave\Events\LeaveRequestApproved;
use Modules\Leave\Notifications\LeaveApprovedNotification;

class NotifyEmployeeLeaveApproved
{
    /**
     * Handle the event.
     */
    public function handle(
        LeaveRequestApproved $event
    ): void
    {
        $event->leaveRequest
            ->employee
            ->user
            ->notify(
                new LeaveApprovedNotification(
                    $event->leaveRequest
                )
            );
    }
}
 