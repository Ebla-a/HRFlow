<?php
namespace Modules\Performance\Services\v1;

use Modules\Employee\Entities\Employee;
use Modules\Performance\Entities\Performance_cycle;
use Modules\Performance\Entities\Performance_review;
use Illuminate\Support\Facades\Auth;
use Modules\Performance\DTO\CreateReviewDTO;

class ReviewService
{
    /**
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function ShowReviews()
    {
        /** @var \Modules\User\Entities\User|null $user */
        $user = Auth::user();
        return Performance_review::with(['cycle', 'employee'])
        ->when($user->hasRole('Manager'), function ($query) use ($user) {
            $query->where('manager_id', $user->employee?->id);
        })
        ->paginate(15);
    }

    /**
     * @param CreateReviewDTO $dto
     * @return Performance_review
     */
    public function createReview(CreateReviewDTO $dto)
    {
        $result=Performance_review::create([
        'performance_cycle_id'=>$dto->performance_cycle_id,
        'employee_id'=>$dto->employee_id,
        'reviewer_id'=>$dto->reviewer_id,
        'status'=>'Reviewed',
        'score'=>$dto->score,
        'comments'=>$dto->comments,
        'reviewed_at'=>now(),
        ]);
        $result->load(['cycle', 'employee']);
        return $result;
    }


    /**
     * @param CreateReviewDTO $dto
     * @param Performance_review $id
     * @return Performance_review
     */
    public function updateReview(CreateReviewDTO $dto, Performance_review $id)
    {
        $id->update([
            'score'=>$dto->score,
            'comments'=>$dto->comments,
            'reviewed_at'=>now(),
        ]);
        $id->load(['cycle', 'employee']);
        return $id;
    }

    /**
     * @param Employee $id
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function employeeReviews(Employee $id,int $perPage = 15)
    {
        return Performance_review::with(['cycle', 'employee'])
        ->where('employee_id',$id->id)->paginate($perPage);
    }

    /**
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function myReviews()
    {
        $employeeId=auth()->id;
        return $this->employeeReviews($employeeId);
    }
    
}