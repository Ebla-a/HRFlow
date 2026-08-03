<?php
namespace Modules\Performance\Policies;
use Modules\Employee\Entities\Employee;
use Modules\Performance\Entities\Performance_cycle;
use Modules\Performance\Entities\Performance_review;

class PerformancePolicy
{
    /**
     * Determine whether the user can view any performance cycles.
     * 
     */
    public function viewCycles($authUser): bool
    {
        if ($authUser->hasRole('Hr_admin') || 
            $authUser->hasRole('Manager')  || 
            $authUser->hasRole('Employee')) 
        { return true; }

        return false;
    }

    /***
     * Determine whether the user can create performance cycles.
     */
    public function createCycle($authUser):bool
    {
        if($authUser->hasRole('Hr_admin')) return true;
        return false;
    }

    /**
     * Determine whether the user can close performance cycles.
     */
    public function updateCycle($authUser):bool
    {
        if($authUser->hasRole('Hr_admin')) return true;
        return false;
    }


    /**
     * Determine whether the user can view their own performance reviews.
     */
    public function viewMyReviews($authUser):bool
    {
        if($authUser->hasRole('Employee')) return true;
        return false;
    }

    /**
     * Determine whether the user can view all of single employee performance reviews.
     */
    public function viewEmployeeReviews($authUser,Employee $targetUser):bool
    {
        if($authUser->hasRole('Hr_admin')) return true;
        if($authUser->hasRole('Manager' ) &&
            $authUser->employee->id == $targetUser->manager_id
        )return true;
        return false;
    }







    /**
     * Determine whether the user can create performance reviews.
     */
    public function createReview($authUser,Employee $targetUser):bool
    {
        $activeCycle = Performance_cycle::where('status', 'Active')->first();
        if($authUser->hasRole('Manager')  && 
            $authUser->employee->id == $targetUser->manager_id  &&
            $activeCycle?->status == 'Active'
            ) 
        return true;

        return false;
    }

    /**
     * Determine whether the user can update performance reviews.
     */
    public function updateReview($authUser,Performance_review $target):bool
    {
        
        if($authUser->hasRole('Manager')  && 
            $authUser->employee->id == $target->reviewer_id &&
            $target->cycle->status == 'Active'
            ) 
        return true;

        return false;
    }

    /**
     * Determine whether the user can view  performance reviews.
     */
    public function performanceReviews($authUser):bool
    {
        if($authUser->hasRole('Hr_admin')) return true;
        
        if( $authUser->hasRole('Manager')) return true;
        return false;
    }



}
