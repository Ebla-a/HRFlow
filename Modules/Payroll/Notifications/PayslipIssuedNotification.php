<?php

namespace Modules\Payroll\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\Payroll\Entities\Payslip;

class PayslipIssuedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly Payslip $payslip
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Monthly Pay Slip Notification')
            ->greeting("Welcom{$notifiable->name}")
            ->line("The payroll cycle has been closed and your pay slip for this month has been posted.")
            ->line("Net Salary" . number_format($this->payslip->net_salary, 2))
            ->action('Show the coupon', config('app.url') . "/payslips/{$this->payslip->id}");
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title'      => 'The new paycheck slip,',
            'payslip_id' => $this->payslip->id,
            'net_salary' => $this->payslip->net_salary,
        ];
    }
}