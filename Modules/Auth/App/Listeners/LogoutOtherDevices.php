<?php

namespace Modules\Auth\App\Listeners;

use Modules\Auth\App\Events\PasswordChanged;

class LogoutOtherDevices
{
    public function handle(PasswordChanged $event)
    {
        $event->user
            ->tokens()
            ->delete();
    }
}
 