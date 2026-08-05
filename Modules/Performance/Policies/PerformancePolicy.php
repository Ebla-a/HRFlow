<?php

namespace Modules\Performance\Policies;

use Modules\Employee\Entities\Employee;
use Modules\Performance\Entities\PerformanceCycle;
use Modules\Performance\Entities\PerformanceReview;

class PerformancePolicy
{
    
    public function viewCycles($authUser): bool
    {
        return  $authUser->hasRole('Hr_admin') || 
                $authUser->hasRole('Manager')  || 
                $authUser->hasRole('Employee');
    }


    public function createCycle($authUser): bool
    {
        return $authUser->hasRole('Hr_admin');
    }

    public function updateCycle($authUser): bool
    {
        return $authUser->hasRole('Hr_admin');
    }

    public function viewMyReviews($authUser): bool
    {
        return $authUser->hasRole('Employee');
    }

    public function viewEmployeeReviews($authUser, Employee $targetUser): bool
    {
        if ($authUser->hasRole('Hr_admin')) {
            return true;
        }

        if ($authUser->hasRole('Manager') && $authUser->employee?->id == $targetUser->manager_id) {
            return true;
        }

        return false;
    }

    public function createReview($authUser, Employee $targetUser, ?PerformanceCycle $cycle = null): bool
    {
        if (!$authUser->hasRole('Manager')) {
            return false;
        }

        $isDirectManager = $authUser->employee?->id == $targetUser->manager_id;
        
        
        $isCycleActive = $cycle ? $cycle->status === 'Active' : PerformanceCycle::where('status', 'Active')->exists();

        return $isDirectManager && $isCycleActive;
    }

    public function updateReview($authUser, PerformanceReview $target): bool
    {
        if ($authUser->hasRole('Manager') && 
            $authUser->employee?->id == $target->reviewer_id &&
            $target->cycle?->status === 'Active') {
            return true;
        }

        return false;
    }

    public function performanceReviews($authUser): bool
    {
        return $authUser->hasRole('Hr_admin') || $authUser->hasRole('Manager');
    }
}