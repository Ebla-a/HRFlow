<?php

namespace Modules\Performance\Services\v1;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Modules\Employee\Entities\Employee;
use Modules\Performance\DTO\CreateReviewDTO;
use Modules\Performance\Entities\PerformanceReview;
use RuntimeException;

class ReviewService
{
    /**
     * Get performance reviews.
     */
    public function showReviews(): LengthAwarePaginator
    {
        return PerformanceReview::query()
            ->with(['cycle', 'employee'])
            ->paginate(15);
    }

    /**
     * Create a performance review.
     *
     * @throws RuntimeException
     */
    public function createReview(CreateReviewDTO $dto): PerformanceReview
    {
        $exists = PerformanceReview::query()
            ->where('performance_cycle_id', $dto->performance_cycle_id)
            ->where('employee_id', $dto->employee_id)
            ->where('reviewer_id', $dto->reviewer_id)
            ->exists();

        if ($exists) {
            throw new RuntimeException(
                __('Employee has already been reviewed in this cycle.')
            );
        }

        $review = PerformanceReview::create([
            'performance_cycle_id' => $dto->performance_cycle_id,
            'employee_id' => $dto->employee_id,
            'reviewer_id' => $dto->reviewer_id,
            'status' => 'Reviewed',
            'score' => $dto->score,
            'comments' => $dto->comments,
            'reviewed_at' => now(),
        ]);

        return $review->load([
            'cycle',
            'employee',
        ]);
    }

    /**
     * Update an existing performance review.
     */
    public function updateReview(
        CreateReviewDTO $dto,
        PerformanceReview $review
    ): PerformanceReview {
        $review->update([
            'score' => $dto->score,
            'comments' => $dto->comments,
            'reviewed_at' => now(),
        ]);

        return $review->load([
            'cycle',
            'employee',
        ]);
    }

    /**
     * Get all reviews belonging to a specific employee.
     */
    public function employeeReviews(
        Employee $employee,
        int $perPage = 15
    ): LengthAwarePaginator {
        return PerformanceReview::query()
            ->with(['cycle', 'employee'])
            ->where('employee_id', $employee->id)
            ->paginate($perPage);
    }

    /**
     * Get reviews belonging to the authenticated employee.
     */
    public function myReviews(): LengthAwarePaginator
    {
        $user = Auth::user();

        $employeeId = $user?->employee?->id;

        /*
         * An authenticated user without an employee record
         * cannot have performance reviews.
         */
        if (!$employeeId) {
            return PerformanceReview::query()
                ->whereRaw('1 = 0')
                ->paginate(15);
        }

        return PerformanceReview::query()
            ->with(['cycle', 'employee'])
            ->where('employee_id', $employeeId)
            ->whereHas('cycle', function ($query) {
                $query->where('status', 'Closed');
            })
            ->paginate(15);
    }
}