<?php

namespace Modules\Leave\Enums;

enum LeaveBalanceStatusEnum: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
}