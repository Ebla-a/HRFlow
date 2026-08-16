<?php

namespace Modules\Performance\Policies;

use Modules\User\Entities\User;
use Modules\Employee\Entities\Employee;
use Modules\Performance\Entities\PerformanceCycle;
use Modules\Performance\Entities\PerformanceReview;
use Illuminate\Auth\Access\Response;

class PerformancePolicy
{
    /**
     * HR/Admin can view all cycles.
     */
    public function viewCycles(User $authUser): bool
    {
        return $authUser->hasPermissionTo('view.performance.cycle.all');
    }

    /**
     * HR/Admin can create cycles.
     */
    public function createCycle(User $authUser): bool
    {
        return $authUser->hasPermissionTo('create.performance.cycle');
    }

    /**
     * HR/Admin can update cycles.
     */
    public function updateCycle(User $authUser): bool
    {
        return $authUser->hasPermissionTo('update.performance.cycle');
    }

    /**
     * Employee can view their own reviews.
     */
    public function viewMyReviews(User $authUser): bool
    {
        return $authUser->hasPermissionTo('view.performance.reviews.own');
    }

    /**
     * HR can view all reviews.
     * Manager can view reviews of employees in their department.
     */
    public function viewReviews(User $authUser, ?Employee $targetEmployee = null): bool
    {
        // HR/Admin override
        if ($authUser->hasPermissionTo('view.reviews.all')) {
            return true;
        }

        // Manager permission
        if (!$authUser->hasPermissionTo('view.reviews.department')) {
            return false;
        }

        // Listing all reviews (no employee filter)
        if (!$targetEmployee) {
            return true;
        }

        // Manager must have employee record
        $managerEmployee = $authUser->employee;
        if (!$managerEmployee) {
            return false;
        }

        // Manager can view only employees in same department
        return (int) $managerEmployee->department_id === (int) $targetEmployee->department_id;
    }

    /**
     * Create review logic:
     * - HR can review any employee if cycle is Active.
     * - Manager can review employees in their department.
     * - If employee has no manager → HR can review.
     * - Manager must be direct manager.
     * - No duplicate reviews.
     */
  public function createReview(User $authUser, Employee $targetEmployee, ?PerformanceCycle $cycle = null)
{
    // Cycle must be active
    if (!$cycle || $cycle->status !== 'Active') {
        return false;
    }

    // 1) Prevent duplicate reviews FIRST
    $alreadyReviewed = PerformanceReview::query()
        ->where('employee_id', $targetEmployee->id)
        ->where('performance_cycle_id', $cycle->id)
        ->exists();

    if ($alreadyReviewed) {
        return Response::deny('Employee has already been reviewed in this cycle.');
    }

    // 2) HR/Admin override
    if ($authUser->hasPermissionTo('view.reviews.all')) {
        return Response::allow();
    }

    // 3) Manager permission
    if (!$authUser->hasPermissionTo('create.review.employee.own.department')) {
        return false;
    }

    // Manager must have employee record
    $managerEmployee = $authUser->employee;
    if (!$managerEmployee) {
        return false;
    }

    // If employee has no manager → only HR can review (already handled above)
    if (!$targetEmployee->manager_id) {
        return false;
    }

    // 4) Manager must be direct manager
    if ((int) $targetEmployee->manager_id !== (int) $managerEmployee->id) {
        return false;
    }

    return Response::allow();
}

    /**
     * Update review logic:
     * - HR can update any review if cycle is Active.
     * - Manager can update only reviews they created.
     */
    public function updateReview(User $authUser, PerformanceReview $review): bool
    {
        // Cycle must be active
        if ($review->cycle?->status !== 'Active') {
            return false;
        }

        // HR/Admin override
        if ($authUser->hasPermissionTo('view.reviews.all')) {
            return true;
        }

        // Manager permission
        if (!$authUser->hasPermissionTo('update.review.employee.own.department')) {
            return false;
        }

        // Manager must have employee record
        $managerEmployee = $authUser->employee;
        if (!$managerEmployee) {
            return false;
        }

        // Manager can update only reviews they created
        return (int) $review->reviewer_id === (int) $managerEmployee->id;
    }
}
