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
 * evaluation cycles and employee review creation based purely on permissions.
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
        return $authUser->hasPermissionTo('view.performance.cycle.all');
    }

    /**
     * Determine whether the user can create a performance cycle.
     *
     * @param User $authUser
     * @return bool
     */
    public function createCycle(User $authUser): bool
    {
        return $authUser->hasPermissionTo('create.performance.cycle');
    }

    /**
     * Determine whether the user can update (activate/close) a performance cycle.
     *
     * @param User $authUser
     * @return bool
     */
    public function updateCycle(User $authUser): bool
    {
        return $authUser->hasPermissionTo('update.performance.cycle');
    }

    /**
     * Determine whether the user can view their own performance reviews.
     *
     * @param User $authUser
     * @return bool
     */
    public function viewMyReviews(User $authUser): bool
    {
        return $authUser->hasPermissionTo('view.performance.reviews.own');
    }

    /**
     * Determine whether the user can view list of performance reviews.
     *
     * @param User $authUser
     * @return bool
     */
    public function viewReviews(User $authUser): bool
    {
        return $authUser->hasPermissionTo('view.reviews.all') || 
               $authUser->hasPermissionTo('view.reviews.department');
    }

    /**
     * Determine whether the logged-in user can submit a review for a specific employee.
     *
     * @param User $authUser
     * @param Employee $targetEmployee
     * @param Performance_cycle|null $cycle
     * @return bool
     */
public function createReview(User $authUser, Employee $targetEmployee, ?Performance_cycle $cycle = null): bool
    {
        // 1. Check direct permission for creating review in department
        $hasPermission = $authUser->hasPermissionTo('create.review.employee.own.department') ||
                         $authUser->hasPermissionTo('view.reviews.all'); // HR Admin override via permission

        if (!$hasPermission) {
            return false;
        }

        // 2. HR with 'view.reviews.all' can review anyone without manager constraint
        if ($authUser->hasPermissionTo('view.reviews.all')) {
            return true;
        }

        // 3. Ensure the authenticated user has a linked employee record
        $managerEmployee = $authUser->employee;
        if (!$managerEmployee) {
            return false;
        }

        // 4. Verify target employee reports directly to this manager
        $isDirectManager = (int) $targetEmployee->manager_id === (int) $managerEmployee->id;

        // 5. Verify the performance cycle status is Active
        $activeCycle = $cycle;
        if (!$activeCycle) {
            $cycleId = request('performance_cycle_id');
            $activeCycle = Performance_cycle::where('id', $cycleId)->first();
        }

        if (!$activeCycle || $activeCycle->status !== 'Active') {
            return false;
        }

        // 6. Ensure duplicate reviews are prevented within the same cycle
        $alreadyReviewed = Performance_review::where('employee_id', $targetEmployee->id)
            ->where('reviewer_id', $managerEmployee->id)
            ->where('performance_cycle_id', $activeCycle->id)
            ->exists();

        return $isDirectManager && !$alreadyReviewed;
    }}