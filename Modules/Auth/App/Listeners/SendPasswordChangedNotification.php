<?php

namespace Modules\Auth\App\Listeners;

use Modules\Auth\App\Events\PasswordChanged;
use Modules\Auth\App\Notifications\PasswordChangedNotification;

class SendPasswordChangedNotification
{
    public function handle(PasswordChanged $event)
    {
        $event->user->notify(
            new PasswordChangedNotification()
        );
    }
}