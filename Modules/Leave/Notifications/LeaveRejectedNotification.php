<?php

namespace Modules\Leave\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Modules\Leave\Entities\LeaveRequest;

class LeaveRejectedNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected LeaveRequest $leaveRequest
    ) {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)

            ->subject('Leave Request Rejected')

            ->line(
                'Your leave request has been rejected.'
            )

            ->line(
                'Reason: ' .
                $this->leaveRequest->rejection_reason
            );
    }
}
 