<?php

namespace Modules\Performance\Policies;

use Modules\Employee\Entities\Employee;
use Modules\Performance\Entities\Performance_cycle;
use Modules\Performance\Entities\Performance_review;

class PerformancePolicy
{
    public function viewCycles($authUser): bool
    {
        return $authUser->hasRole('Hr_admin') || 
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

    public function createReview($authUser, Employee $targetUser, ?Performance_cycle $cycle = null): bool
    {
        if (!$authUser->hasRole('Manager')) {
            return false;
        }

        $isDirectManager = $authUser->employee?->id == $targetUser->manager_id;
        
       
        $isCycleActive = $cycle ? $cycle->status === 'Active' : Performance_cycle::where('status', 'Active')->exists();

        return $isDirectManager && $isCycleActive;
    }

    public function updateReview($authUser, Performance_review $target): bool
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