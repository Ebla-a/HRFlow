<?php

namespace Modules\Leave\Observers;

use Illuminate\Support\Facades\Log;
use Modules\Leave\Entities\LeaveRequest;
use Modules\Leave\Events\LeaveRequestCreated;
use Modules\Leave\Events\LeaveRequestApproved;
use Modules\Leave\Events\LeaveRequestRejected;

class LeaveRequestObserver
{
    /**
     * Handle the LeaveRequest "created" event.
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
     * Handle the LeaveRequest "updated" event.
     */
    public function updated(
        LeaveRequest $leaveRequest
    ): void {
        if ($leaveRequest->isDirty('status')) {

            $oldStatus = $leaveRequest->getOriginal('status');

            Log::info(
                'Leave request status changed',
                [
                    'leave_request_id' => $leaveRequest->id,
                    'old_status' => $oldStatus,
                    'new_status' => $leaveRequest->status,
                ]
            );

            if (
                $leaveRequest->status === 'approved'
                &&
                $oldStatus !== 'approved'
            ) {
                event(
                    new LeaveRequestApproved(
                        $leaveRequest
                    )
                );
            }

            if (
                $leaveRequest->status === 'rejected'
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
}