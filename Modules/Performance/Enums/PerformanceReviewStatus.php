<?php
namespace Modules\Performance\Enums;

enum PerformanceReviewStatus: string
{
    case Reviewed = 'Reviewed';
    case DRAFT  = 'Draft';
}