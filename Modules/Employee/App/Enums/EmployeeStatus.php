<?php

namespace Modules\Employee\App\Enums;

enum EmployeeStatus: string
{
    case ACTIVE = 'active';
    case ON_LEAVE = 'on_leave';
    case SUSPENDED = 'suspended';
    case TERMINATED = 'terminated';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}