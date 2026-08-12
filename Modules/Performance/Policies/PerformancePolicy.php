<?php

namespace Modules\Performance\Policies;

use Modules\User\Entities\User;
use Modules\Employee\Entities\Employee;
use Modules\Performance\Entities\PerformanceCycle;
use Modules\Performance\Entities\PerformanceReview;

/**
 * Class PerformancePolicy
 *
 * Handles authorization for performance cycles and employee reviews.
 *
 * Authorization is permission-based rather than role-based.
 */
class PerformancePolicy
{
    /**
     * Determine whether the user can view performance cycles.
     */
    public function viewCycles(User $authUser): bool
    {
        return $authUser->hasPermissionTo(
            'view.performance.cycle.all'
        );
    }

    /**
     * Determine whether the user can create a performance cycle.
     */
    public function createCycle(User $authUser): bool
    {
        return $authUser->hasPermissionTo(
            'create.performance.cycle'
        );
    }

    /**
     * Determine whether the user can update a performance cycle.
     *
     * Used for activating and closing cycles.
     */
    public function updateCycle(User $authUser): bool
    {
        return $authUser->hasPermissionTo(
            'update.performance.cycle'
        );
    }

    /**
     * Determine whether the user can view their own reviews.
     */
    public function viewMyReviews(User $authUser): bool
    {
        return $authUser->hasPermissionTo(
            'view.performance.reviews.own'
        );
    }

    /**
     * Determine whether the user can view performance reviews.
     *
     * HR/Admin can view all reviews.
     * Managers can view reviews within their department.
     */
    public function viewReviews(
        User $authUser,
        ?Employee $targetEmployee = null
    ): bool {
        /*
         * HR/Admin override.
         */
        if ($authUser->hasPermissionTo('view.reviews.all')) {
            return true;
        }

        /*
         * User must have department-level review permission.
         */
        if (!$authUser->hasPermissionTo('view.reviews.department')) {
            return false;
        }

        /*
         * General performance review listing.
         */
        if (!$targetEmployee) {
            return true;
        }

        /*
         * The authenticated user must have an employee record.
         */
        $managerEmployee = $authUser->employee;

        if (!$managerEmployee) {
            return false;
        }

        /*
         * Manager can only view employees from
         * the same department.
         */
        return (int) $managerEmployee->department_id ===
            (int) $targetEmployee->department_id;
    }

    /**
     * Determine whether the authenticated user can create
     * a performance review for a specific employee and cycle.
     */
    public function createReview(
        User $authUser,
        Employee $targetEmployee,
        ?PerformanceCycle $cycle = null
    ): bool {
        /*
         * HR/Admin override.
         */
        if ($authUser->hasPermissionTo('view.reviews.all')) {
            return $cycle?->status === 'Active';
        }

        /*
         * Manager must have permission to create reviews
         * for employees within their own department.
         */
        if (!$authUser->hasPermissionTo(
            'create.review.employee.own.department'
        )) {
            return false;
        }

        /*
         * Authenticated user must have an employee record.
         */
        $managerEmployee = $authUser->employee;

        if (!$managerEmployee) {
            return false;
        }

        /*
         * A valid active cycle is required.
         */
        if (!$cycle || $cycle->status !== 'Active') {
            return false;
        }

        /*
         * The reviewer must be the direct manager
         * of the target employee.
         */
        $isDirectManager =
            (int) $targetEmployee->manager_id ===
            (int) $managerEmployee->id;

        if (!$isDirectManager) {
            return false;
        }

        /*
         * Prevent duplicate reviews for the same employee,
         * reviewer and performance cycle.
         */
        $alreadyReviewed = PerformanceReview::query()
            ->where('employee_id', $targetEmployee->id)
            ->where('reviewer_id', $managerEmployee->id)
            ->where('performance_cycle_id', $cycle->id)
            ->exists();

        return !$alreadyReviewed;
    }

    /**
     * Determine whether the authenticated user can update
     * an existing performance review.
     *
     * HR/Admin can update any review.
     * Managers can update only reviews they created.
     * Reviews can only be updated while the cycle is active.
     */
    public function updateReview(
        User $authUser,
        PerformanceReview $review
    ): bool {
        /*
         * HR/Admin override.
         */
        if ($authUser->hasPermissionTo('view.reviews.all')) {
            return $review->cycle?->status === 'Active';
        }

        /*
         * Manager must have update permission.
         */
        if (!$authUser->hasPermissionTo(
            'update.review.employee.own.department'
        )) {
            return false;
        }

        /*
         * Authenticated user must have an employee record.
         */
        $managerEmployee = $authUser->employee;

        if (!$managerEmployee) {
            return false;
        }

        /*
         * Only the reviewer who created the review
         * can update it.
         */
        if (
            (int) $review->reviewer_id !==
            (int) $managerEmployee->id
        ) {
            return false;
        }

        /*
         * Reviews cannot be modified after the cycle is closed.
         */
        return $review->cycle?->status === 'Active';
    }
}