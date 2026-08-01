<?php

namespace Modules\Employee\App\Enums;

enum EmploymentType: string
{
    case FULL_TIME = 'full_time';
    case PART_TIME = 'part_time';
    case CONTRACT = 'contract';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}