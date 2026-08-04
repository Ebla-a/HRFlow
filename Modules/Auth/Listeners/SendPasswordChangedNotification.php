<?php

namespace Modules\Auth\Listeners;

use Modules\Auth\Events\PasswordChanged;
use Modules\Auth\Notifications\PasswordChangedNotification;

class SendPasswordChangedNotification
{
    public function handle(PasswordChanged $event)
    {
        $event->user->notify(
            new PasswordChangedNotification()
        );
    }
}
 