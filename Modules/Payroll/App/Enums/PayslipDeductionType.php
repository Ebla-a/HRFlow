<?php

namespace Modules\Payroll\App\Enums;

enum PayslipDeductionType: string
{
    case Loan = 'loan';

    case Tax = 'tax';

    case Insurance = 'insurance';

    case Penalty = 'penalty';

    case Other = 'other';
}