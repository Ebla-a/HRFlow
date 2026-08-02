<?php

namespace Modules\Employee\App\Listeners;

use Modules\Employee\App\Events\EmployeeHired;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendWelcomeEmailListener implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(EmployeeHired $event): void
    {
        Log::info("Sending welcome email to employee: {$event->employee->user->email} with temp password: {$event->temporaryPassword}");
    }
}