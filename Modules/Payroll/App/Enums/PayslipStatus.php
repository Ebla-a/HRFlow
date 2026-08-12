<?php 

namespace Modules\Payroll\App\Enums;


enum PayslipStatus: string
{
    case DRAFT = 'draft';
    case GENERATED = 'generated';
    case PAID = 'paid';
}