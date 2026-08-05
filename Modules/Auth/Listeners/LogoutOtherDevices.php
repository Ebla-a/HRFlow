<?php

namespace Modules\Auth\Listeners;

use Modules\Auth\Events\PasswordChanged;

class LogoutOtherDevices
{
    public function handle(PasswordChanged $event)
    {
        $event->user
            ->tokens()
            ->delete();
    }
}
 