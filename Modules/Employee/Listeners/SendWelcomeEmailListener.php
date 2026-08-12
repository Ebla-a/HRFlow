<?php

namespace Modules\Employee\Listeners;


use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Modules\Employee\Events\EmployeeHired;

class SendWelcomeEmailListener implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(EmployeeHired $event): void
    {
        Log::info("Sending welcome email to employee: {$event->employee->user->email} with temp password: {$event->temporaryPassword}");
    }
}