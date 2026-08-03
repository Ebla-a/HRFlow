<?php

namespace Modules\Leave\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\Leave\Entities\LeaveRequest;

class LeaveApprovedNotification extends Notification
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
            ->subject('Leave Request Approved')
            ->line('Your leave request has been approved.')
            ->line(
                'From: ' . $this->leaveRequest->start_date
            )
            ->line(
                'To: ' . $this->leaveRequest->end_date
            );
    }
}
 