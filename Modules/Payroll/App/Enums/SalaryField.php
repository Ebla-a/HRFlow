<?php

namespace Modules\Payroll\App\Enums;

enum SalaryField: string
{
    case BasicSalary = 'basic_salary';

    case HousingAllowance = 'housing_allowance';

    case TransportAllowance = 'transport_allowance';

    case OtherAllowance = 'other_allowance';
}