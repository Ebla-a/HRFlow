<?php

namespace Modules\Payroll\App\Enums;

enum PayslipDeductionType: string
{
    case MANUAL = 'manual';
    case SYSTEM = 'system';
    case LOAN = 'loan';
    case ABSENCE = 'absence';
    case PENALTY = 'penalty';
}