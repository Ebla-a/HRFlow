<?php

namespace Modules\Performance\Services\v1;

use Modules\Employee\Entities\Employee;
use Modules\Performance\Entities\Performance_review;
use Illuminate\Support\Facades\Auth;
use Modules\Performance\DTO\CreateReviewDTO;
use Modules\Performance\Entities\PerformanceReview;

class ReviewService
{
    /**
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function showReviews()
    {
        /** @var \Modules\User\Entities\User|null $user */
        $user = Auth::user();

        return PerformanceReview::with(['cycle', 'employee'])
            ->when($user->hasRole('Manager'), function ($query) use ($user) {
                $query->where('reviewer_id', $user->employee?->id);
            })
            ->paginate(15);
    }

    /**
     * @param CreateReviewDTO $dto
     * @return PerformanceReview
     * @throws \Exception
     */
    public function createReview(CreateReviewDTO $dto)
    {
        $exists = PerformanceReview::where('performance_cycle_id', $dto->performance_cycle_id)
            ->where('employee_id', $dto->employee_id)
            ->where('reviewer_id', $dto->reviewer_id)
            ->exists();

        if ($exists) {
            throw new \Exception('Employee has already been reviewed in this cycle.');
        }

        $result = PerformanceReview::create([
            'performance_cycle_id' => $dto->performance_cycle_id,
            'employee_id'          => $dto->employee_id,
            'reviewer_id'          => $dto->reviewer_id,
            'status'               => 'Reviewed',
            'score'                => $dto->score,
            'comments'             => $dto->comments,
            'reviewed_at'          => now(),
        ]);

        $result->load(['cycle', 'employee']);
        return $result;
    }

    /**
     * @param CreateReviewDTO $dto
     * @param PerformanceReview $review
     * @return PerformanceReview
     */
    public function updateReview(CreateReviewDTO $dto, PerformanceReview $review)
    {
        $review->update([
            'score'       => $dto->score,
            'comments'    => $dto->comments,
            'reviewed_at' => now(),
        ]);

        $review->load(['cycle', 'employee']);
        return $review;
    }

    /**
     * @param Employee $employee
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function employeeReviews(Employee $employee, int $perPage = 15)
    {
        return PerformanceReview::with(['cycle', 'employee'])
            ->where('employee_id', $employee->id)
            ->paginate($perPage);
    }

    /**
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function myReviews()
    {
        $user = Auth::user();
        $employeeId = $user->employee?->id;

        return PerformanceReview::with(['cycle', 'employee'])
            ->where('employee_id', $employeeId)
            ->whereHas('cycle', function ($q) {
                $q->where('status', 'Closed');
            })
            ->paginate(15);
    }
}