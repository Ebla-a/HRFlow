<?php

namespace Modules\Leave\Listeners;

use Modules\Leave\Events\LeaveRequestCreated;
use Modules\Leave\Notifications\LeaveRequestNotification;

class NotifyManagerAboutLeaveRequest
{
    public function handle(
        LeaveRequestCreated $event
    ): void
    {
        $employee = $event->leaveRequest->employee;

         if (!$employee) {
           return;
         }

        if ($employee?->manager?->user) {

            $employee->manager->user->notify(
                new LeaveRequestNotification(
                    $event->leaveRequest
                )
            );

        }
    }
}
 