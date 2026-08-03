<?php

namespace Modules\Payroll\App\Enums;

enum PayrollRunStatus: string
{
    case Draft = 'draft';

    case Processing = 'processing';

    case Finalized = 'finalized';
}