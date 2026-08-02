<?php

namespace Modules\Leave\Services;

use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use Modules\Leave\DTO\LeaveRequestDTO;
use Modules\Leave\Entities\LeaveBalance;
use Modules\Leave\Entities\LeaveRequest;
use Modules\Leave\Events\LeaveRequestApproved;
use Modules\Leave\Events\LeaveRequestCreated;
use Modules\Leave\Events\LeaveRequestRejected;
use Modules\Leave\Repositories\Interfaces\LeaveRequestRepositoryInterface;

class LeaveRequestService
{
    public function __construct(
        protected LeaveRequestRepositoryInterface $repository
    ) {
    }

    public function create(
        LeaveRequestDTO $dto
    ): LeaveRequest {

        $data = $dto->toArray();

        $daysCount = $this->calculateDays(
            $data['start_date'],
            $data['end_date']
        );

        $this->checkOverlap(
            $data['employee_id'],
            $data['start_date'],
            $data['end_date']
        );

        $this->checkBalance(
            $data['employee_id'],
            $data['leave_type_id'],
            $daysCount
        );

        $data['days_count'] = $daysCount;
        $data['status'] = 'pending';
        $data['manager_approval_status'] = 'pending';
        $data['hr_approval_status'] = 'pending';

        $leaveRequest = $this->repository->create($data);

        event(
          new LeaveRequestCreated(
          $leaveRequest->refresh()
         )
        ); 
        return $leaveRequest;
    }

    private function calculateDays(
        string $startDate,
        string $endDate
    ): int {

        return Carbon::parse($startDate)
            ->diffInDays(
                Carbon::parse($endDate)
            ) + 1;
    }

    private function checkOverlap(
        int $employeeId,
        string $startDate,
        string $endDate
    ): void {

        $exists = LeaveRequest::where(
            'employee_id',
            $employeeId
        )
        ->whereIn('status', [
            'pending',
            'approved'
        ])
        ->where(function ($query) use (
            $startDate,
            $endDate
        ) {

            $query->where(
                'start_date',
                '<=',
                $endDate
            )
            ->where(
                'end_date',
                '>=',
                $startDate
            );
        })
        ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'date' => 'You already have a leave request in this period.'
            ]);
        }
    }

    private function checkBalance(
        int $employeeId,
        int $leaveTypeId,
        int $days
    ): void {

        $balance = LeaveBalance::where(
            'employee_id',
            $employeeId
        )
        ->where(
            'leave_type_id',
            $leaveTypeId
        )
        ->where(
            'year',
            now()->year
        )
        ->first();

        if (!$balance) {
            throw ValidationException::withMessages([
                'balance' => 'Leave balance not found.'
            ]);
        }

        if ($balance->remaining_days < $days) {
            throw ValidationException::withMessages([
                'balance' => 'Insufficient leave balance.'
            ]);
        }
    }

    public function approveByManager(
        LeaveRequest $leaveRequest
    ): LeaveRequest {

        if ($leaveRequest->status !== 'pending') {
            throw ValidationException::withMessages([
                'status' => 'Leave request already processed.'
            ]);
        }

        $leaveRequest->update([
            'manager_approval_status' => 'approved',
            'manager_approved_at' => now(),
        ]);

        return $leaveRequest->refresh();
    }

    public function approveByHR(
        LeaveRequest $leaveRequest
    ): LeaveRequest {

        if ($leaveRequest->status === 'approved') {
            throw ValidationException::withMessages([
                'status' => 'Leave request already approved.'
            ]);
        }

        if ($leaveRequest->manager_approval_status !== 'approved') {
            throw ValidationException::withMessages([
                'approval' => 'Manager approval is required first.'
            ]);
        }

        $leaveRequest->update([
            'hr_approval_status' => 'approved',
            'hr_approved_at' => now(),
            'status' => 'approved',
        ]);

        $this->updateBalance(
            $leaveRequest
        );

        $leaveRequest = $leaveRequest->refresh();

        event(
           new LeaveRequestApproved(
           $leaveRequest
         )
        ); 
        return $leaveRequest;
    }

    private function updateBalance(
        LeaveRequest $leaveRequest
    ): void {
        $balance = LeaveBalance::where(
            'employee_id',
            $leaveRequest->employee_id
        )
        ->where(
            'leave_type_id',
            $leaveRequest->leave_type_id
        )
        ->where(
            'year',
            now()->year
        )
        ->first();

        if (!$balance) {
            throw ValidationException::withMessages([
                'balance' => 'Leave balance not found.'
            ]);
        }

        $balance->decrement(
            'remaining_days',
            $leaveRequest->days_count
        );

        $balance->increment(
            'used_days',
            $leaveRequest->days_count
        );
    }

    public function reject(
        LeaveRequest $leaveRequest,
        string $reason
    ): LeaveRequest {

        if ($leaveRequest->status === 'approved') {
            throw ValidationException::withMessages([
                'status' => 'Approved request cannot be rejected.'
            ]);
        }

        $leaveRequest->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
        ]);

        $leaveRequest = $leaveRequest->refresh();

        event(
           new LeaveRequestRejected(
           $leaveRequest
          )
        );

        return $leaveRequest;
    }
}
