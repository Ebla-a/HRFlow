<?php

namespace Modules\Leave\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Leave\Events\LeaveRequestApproved;
use Modules\Leave\Notifications\LeaveApprovedNotification;

class NotifyEmployeeLeaveApproved implements ShouldQueue
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
 