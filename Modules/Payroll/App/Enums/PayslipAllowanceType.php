<?php

namespace Modules\Payroll\App\Enums;

enum PayslipAllowanceType: string
{
    case Bonus = 'bonus';
    case Overtime = 'overtime';
    case Performance = 'performance';
    case Other = 'other';
}