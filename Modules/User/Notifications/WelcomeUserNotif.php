<?php

namespace Modules\User\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeUserNotif extends Notification implements ShouldQueue
{
    use Queueable;

    public $tempPassword;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $tempPassword)
    {
        $this->tempPassword = $tempPassword;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Welcome to our company')
            ->view('user::emails.welcome', [
                'email' => $notifiable->routeNotificationFor('mail'),
                'tempPassword' => $this->tempPassword,
            ]);
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}