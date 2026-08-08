<?php

namespace Modules\Performance\Policies;

use Modules\User\Entities\User;
use Modules\Employee\Entities\Employee;
use Modules\Performance\Entities\Performance_cycle;
use Modules\Performance\Entities\Performance_review;

/**
 * Class PerformancePolicy
 * 
 * Handles access control for performance management features, including
 * evaluation cycles and employee review creation.
 */
class PerformancePolicy
{
    /**
     * Determine whether the user can view performance cycles.
     *
     * @param User $authUser
     * @return bool
     */
    public function viewCycles(User $authUser): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create a performance cycle.
     * Exclusive to HR Admin.
     *
     * @param User $authUser
     * @return bool
     */
    public function createCycle(User $authUser): bool
    {
        return $authUser->hasRole('Hr_admin') || $authUser->hasPermissionTo('create.performance.cycle');
    }

    /**
     * Determine whether the user can update (activate/close) a performance cycle.
     * Exclusive to HR Admin.
     *
     * @param User $authUser
     * @return bool
     */
    public function updateCycle(User $authUser): bool
    {
        return $authUser->hasRole('Hr_admin') || $authUser->hasPermissionTo('update.performance.cycle');
    }

    /**
     * Determine whether the user can view their own performance reviews.
     *
     * @param User $authUser
     * @return bool
     */
    public function viewMyReviews(User $authUser): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view list of performance reviews.
     *
     * @param User $authUser
     * @return bool
     */
    public function viewReviews(User $authUser): bool
    {
        return $authUser->hasRole('Hr_admin') || 
               $authUser->hasRole('Manager') || 
               $authUser->hasPermissionTo('view.reviews.all') || 
               $authUser->hasPermissionTo('view.reviews.department');
    }

    /**
     * Determine whether the logged-in user can submit a review for a specific employee.
     * 
     * Requirements according to HRFlow spec:
     * 1. HR Admins are bypassed for system administrative override.
     * 2. Reviewer must have an associated employee profile.
     * 3. Target employee must be a direct report (`manager_id`).
     * 4. The selected performance cycle must be currently 'Active'.
     * 5. One evaluation per employee per manager in the same cycle.
     *
     * @param User $authUser
     * @param Employee $targetEmployee
     * @param Performance_cycle|null $cycle
     * @return bool
     */
    public function createReview(User $authUser, Employee $targetEmployee, ?Performance_cycle $cycle = null): bool
    {
        // 1. Bypass check for HR Admin roles
        if ($authUser->hasRole('Hr_admin')) {
            return true;
        }

        // 2. Ensure the authenticated user has a linked employee record
        $managerEmployee = $authUser->employee;
        if (!$managerEmployee) {
            return false;
        }

        // 3. Verify target employee reports directly to this manager
        $isDirectManager = (int) $targetEmployee->manager_id === (int) $managerEmployee->id;

        // 4. Verify the performance cycle status is Active
        $cycleId = request('performance_cycle_id');
        $isCycleActive = $cycle 
            ? $cycle->status === 'Active' 
            : Performance_cycle::where('status', 'Active')->where('id', $cycleId)->exists();

        // 5. Ensure duplicate reviews are prevented within the same cycle
        $alreadyReviewed = Performance_review::where('employee_id', $targetEmployee->id)
            ->where('reviewer_id', $managerEmployee->id)
            ->where('performance_cycle_id', $cycleId)
            ->exists();

        return $isDirectManager && $isCycleActive && !$alreadyReviewed;
    }

    /**
     * Determine whether the user can update an existing performance review prior to cycle closure.
     *
     * @param User $authUser
     * @param Performance_review $review
     * @return bool
     */
    public function updateReview(User $authUser, Performance_review $review): bool
    {
        $managerEmployee = $authUser->employee;
        if (!$managerEmployee) {
            return false;
        }

        $isReviewer = (int) $review->reviewer_id === (int) $managerEmployee->id;
        $isCycleActive = $review->cycle && $review->cycle->status === 'Active';

        return $isReviewer && $isCycleActive;
    }
}