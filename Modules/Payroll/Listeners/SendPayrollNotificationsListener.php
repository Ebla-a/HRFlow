<?php
namespace Modules\Payroll\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Payroll\Events\PayrollFinalized;
use Modules\Payroll\Notifications\PayslipIssuedNotification;

class SendPayrollNotificationsListener implements ShouldQueue
{
  public function handle(PayrollFinalized $event): void
{
    $payrollRun = $event->payrollRun->load('payslips.employee.user');

    foreach ($payrollRun->payslips as $payslip) {
        if ($payslip->employee?->user) {
            $payslip->employee->user->notify(new PayslipIssuedNotification($payslip));
        }
    }
}
}