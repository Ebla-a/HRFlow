<?php

namespace Modules\Leave\Observers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Modules\Leave\Entities\LeaveRequest;
use Modules\Leave\Events\LeaveRequestCreated;
use Modules\Leave\Events\LeaveRequestApproved;
use Modules\Leave\Events\LeaveRequestRejected;
use Modules\Leave\Enums\LeaveRequestStatusEnum;

class LeaveRequestObserver
{
    /**
     * Calculate days before creating leave request
     */
    public function creating(
        LeaveRequest $leaveRequest
    ): void {

        $leaveRequest->days_count =
            Carbon::parse($leaveRequest->start_date)
            ->diffInDays(
                Carbon::parse($leaveRequest->end_date)
            ) + 1;
    }

    /**
     * Handle created event
     */
    public function created(
        LeaveRequest $leaveRequest
    ): void {

        Log::info(
            'New leave request created',
            [
                'leave_request_id' => $leaveRequest->id,
                'employee_id' => $leaveRequest->employee_id,
            ]
        );

        event(
            new LeaveRequestCreated(
                $leaveRequest
            )
        );
    }

    /**
     * Handle updated event
     */
    public function updated(
        LeaveRequest $leaveRequest
    ): void {

        if ($leaveRequest->isDirty('status')) {
            return;
        }

            $oldStatus =
                $leaveRequest->getOriginal('status');

            Log::info(
                'Leave request status changed',
                [
                    'leave_request_id'
                    => $leaveRequest->id,

                    'old_status'
                    => $oldStatus,

                    'new_status'
                    => $leaveRequest->status,
                ]
            );

            if (
                $leaveRequest->status === LeaveRequestStatusEnum::APPROVED                &&
                    $oldStatus !== 'approved'
            ) {
                event(
                    new LeaveRequestApproved(
                        $leaveRequest
                    )
                );
            }

            if (
                $leaveRequest->status === LeaveRequestStatusEnum::REJECTED
                &&
                $oldStatus !== 'rejected'
            ) {
                event(
                    new LeaveRequestRejected(
                        $leaveRequest
                    )
                );
        }
    }
}
