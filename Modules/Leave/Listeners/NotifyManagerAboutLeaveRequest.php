<?php

namespace Modules\Leave\Listeners;

use Modules\User\Entities\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use Modules\Leave\Events\LeaveRequestCreated;
use Modules\Leave\Notifications\LeaveRequestNotification;


class NotifyManagerAboutLeaveRequest implements ShouldQueue
{
    public function handle(
        LeaveRequestCreated $event
    ): void
    {
        $employee = $event->leaveRequest->employee;

         if (!$employee) {
           return;
         }
if ($employee->manager?->user) {
            $employee->manager->user->notify(new LeaveRequestNotification($event->leaveRequest));
        } else {
            $hrAdmins = User::role('HR Admin')->get();
            Notification::send($hrAdmins, new LeaveRequestNotification($event->leaveRequest));
        }

        }
    }

 