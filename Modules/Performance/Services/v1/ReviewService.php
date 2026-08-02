<?php
namespace Modules\Performance\Services\v1;

use Modules\Employee\Entities\Employee;
use Modules\Performance\Entities\performance_cycle;
use Modules\Performance\Entities\performance_review;
use Illuminate\Support\Facades\Auth;

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
     * @param array $data
     * @return Performance_review
     */
    public function createReview(array $data)
    {
        $data=Performance_review::create([
        'performance_cycle_id'=>$data['performance_cycle_id'],
        'employee_id'=>$data['employee_id'],
        'reviewer_id'=>$data['reviewer_id'],
        'status'=>'Reviewed',
        'score'=>$data['score'],
        'comments'=>$data['comments'],
        'reviewed_at'=>now(),
        ]);
        $data->load(['cycle', 'employee']);
        return $data;
    }

    /**
     * @param array $data
     * @param Performance_review $id
     * @return Performance_review
     */
    public function updateReview(array $data, performance_review $id)
    {
        $id->update([
            'score'=>$data['score'],
            'comments'=>$data['comments'],
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
        return performance_review::with(['cycle', 'employee'])
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