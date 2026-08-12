<?php

namespace Modules\Leave\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Modules\Leave\DTO\LeaveRequestDTO;
use Modules\Leave\Entities\LeaveBalance;
use Modules\Leave\Entities\LeaveRequest;
use Modules\Leave\Enums\LeaveRequestStatusEnum;
use Modules\Leave\Repositories\Interfaces\LeaveRequestRepositoryInterface;

class LeaveRequestService
{
    public function __construct(
        protected LeaveRequestRepositoryInterface $repository
    ) {
    }

    /**
     * Create new leave request.
     */
    public function create(
        LeaveRequestDTO $dto
    ): LeaveRequest {

        $data = $dto->toArray();

        if (request()->hasFile('attachment')) {

            $data['attachment_path'] = request()
                ->file('attachment')
                ->store(
                    'leave_attachments',
                    'public'
                );
        }

        $this->checkOverlap(
            $data['employee_id'],
            $data['start_date'],
            $data['end_date']
        );

        $this->checkBalance(
            $data['employee_id'],
            $data['leave_type_id'],
            $this->calculateDays(
                $data['start_date'],
                $data['end_date']
            )
        );

        $data['status']
            = LeaveRequestStatusEnum::PENDING->value;

        $data['manager_approval_status']
            = 'pending';

        $data['hr_approval_status']
            = 'pending';

        return $this->repository->create($data);
    }

    /**
     * Calculate leave days.
     */
    private function calculateDays(
        string $startDate,
        string $endDate
    ): int {

        return Carbon::parse($startDate)
            ->diffInDays(
                Carbon::parse($endDate)
            ) + 1;
    }

    /**
     * Prevent overlapping leave requests.
     */
    private function checkOverlap(
        int $employeeId,
        string $startDate,
        string $endDate
    ): void {

        $exists = LeaveRequest::where(
            'employee_id',
            $employeeId
        )
        ->whereIn(
            'status',
            [
                LeaveRequestStatusEnum::PENDING->value,
                LeaveRequestStatusEnum::APPROVED->value,
            ]
        )
        ->where(function ($query) use (
            $startDate,
            $endDate
        ) {
            $query
                ->where(
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
                'date' =>
                'You already have a leave request in this period.',
            ]);
        }
    }

    /**
     * Check employee leave balance.
     */
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
                'balance' => 'Leave balance not found.',
            ]);
        }

        if ($balance->remaining_days < $days) {

            throw ValidationException::withMessages([
                'balance' => 'Insufficient leave balance.',
            ]);
        }
    }

    /**
     * Manager approves leave request.
     */
    public function approveByManager(
      LeaveRequest $leaveRequest
    ): LeaveRequest {

    return DB::transaction(function () use ($leaveRequest) {

        if ($leaveRequest->status !== LeaveRequestStatusEnum::PENDING) {

            throw ValidationException::withMessages([
                'status' => 'Leave request must be pending.',
            ]);
        }

        $leaveRequest->update([
            'manager_approval_status' => 'approved',
            'manager_approved_at' => now(),
            'approved_by' => auth('sanctum')->id(),
        ]);

        return $leaveRequest->refresh();
    });
 }

    /**
     * HR approves leave request.
     */
    public function approveByHR(
        LeaveRequest $leaveRequest
    ): LeaveRequest {

        return DB::transaction(function () use ($leaveRequest) {

            if ($leaveRequest->status !== LeaveRequestStatusEnum::PENDING) {

                throw ValidationException::withMessages([
                    'status' => 'Leave request must be pending.',
                ]);
            }

            if (
                $leaveRequest->manager_approval_status
                !== 'approved'
            ) {
                throw ValidationException::withMessages([
                    'approval' => 'Manager approval is required first.',
                ]);
            }

           $leaveRequest->update([
               'hr_approval_status' => 'approved',
               'hr_approved_at' => now(),
               'hr_approved_by' => auth('sanctum')->id(),
               'status' => LeaveRequestStatusEnum::APPROVED->value,
           ]);

            $this->updateBalance($leaveRequest);

            return $leaveRequest->refresh();
        });
    }

    /**
     * Update employee leave balance.
     */
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
                'balance' => 'Leave balance not found.',
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

    /**
     * Reject leave request.
     */
    public function reject(
        LeaveRequest $leaveRequest,
        string $reason
    ): LeaveRequest {

        if ($leaveRequest->status === LeaveRequestStatusEnum::APPROVED) {
            throw ValidationException::withMessages([
                'status' => 'Approved request cannot be rejected.',
            ]);
        }

        $leaveRequest->update([
            'status' => LeaveRequestStatusEnum::REJECTED->value,
            'rejection_reason' => $reason,
        ]);

        return $leaveRequest->refresh();
    }
}