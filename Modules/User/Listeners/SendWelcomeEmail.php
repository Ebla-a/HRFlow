<?php

namespace Modules\User\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use Modules\User\Events\UserCreated;
use Modules\User\Notifications\WelcomeUserNotif;

class SendWelcomeEmail implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * 
     * @return void
     */
    public function handle(UserCreated $event)
    {
        // Send the notification to the email address passed from the event
        Notification::route('mail', $event->email)
            ->notify(new WelcomeUserNotif($event->tempPassword));
    }
}