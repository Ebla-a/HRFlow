<?php

namespace Modules\Leave\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Modules\Leave\Entities\LeaveRequest;

class LeaveRequestNotification extends Notification
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

            ->subject('New Leave Request')

            ->line(
                'A new leave request has been submitted.'
            )

            ->line(
                'Employee: ' .
                $this->leaveRequest
                ->employee
                ->user
                ->name
            )

            ->line(
                'From: ' .
                $this->leaveRequest->start_date .
                ' To: ' .
                $this->leaveRequest->end_date
            );
    }
}
 