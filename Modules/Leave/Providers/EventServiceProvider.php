<?php

namespace Modules\Leave\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Leave\Events\LeaveRequestCreated;
use Modules\Leave\Events\LeaveRequestApproved;
use Modules\Leave\Events\LeaveRequestRejected;
use Modules\Leave\Listeners\NotifyManagerAboutLeaveRequest;
use Modules\Leave\Listeners\NotifyEmployeeLeaveApproved;
use Modules\Leave\Listeners\NotifyEmployeeLeaveRejected;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [

        LeaveRequestCreated::class => [
            NotifyManagerAboutLeaveRequest::class,
        ],

        LeaveRequestApproved::class => [
            NotifyEmployeeLeaveApproved::class,
        ],

        LeaveRequestRejected::class => [
            NotifyEmployeeLeaveRejected::class,
        ],
    ];
}
 