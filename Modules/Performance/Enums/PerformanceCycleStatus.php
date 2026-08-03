<?php
namespace Modules\Performance\Enums;

enum PerformanceCycleStatus: string
{
    case ACTIVE = 'Active';
    case CLOSED = 'Closed';
    case DRAFT  = 'Draft';
}