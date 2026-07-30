<?php

namespace Modules\Auth\App\Listeners;

use App\Models\PasswordChangeLog;
use Modules\Auth\App\Events\PasswordChanged;

class LogPasswordChange
{
    public function handle(PasswordChanged $event)
    {
        PasswordChangeLog::create([
            'user_id' => $event->user->id,
            'ip_address' => $event->ip,
            'device' => $event->device,
        ]);
    }
}
