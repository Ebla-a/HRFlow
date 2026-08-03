<?php

namespace Modules\Employee\Listeners;

use Modules\Employee\App\Events\EmployeeHired;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Modules\Employee\Events\EmployeeHired as EventsEmployeeHired;

class SendWelcomeEmailListener implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(EventsEmployeeHired $event): void
    {
        Log::info("Sending welcome email to employee: {$event->employee->user->email} with temp password: {$event->temporaryPassword}");
    }
}