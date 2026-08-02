<?php

namespace Modules\Leave\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LeaveRequestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'employee' => [
                'id' => $this->employee?->id,
                'name' => $this->employee?->user?->name,
            ],

            'leave_type' => [
                'id' => $this->leaveType?->id,
                'name' => $this->leaveType?->name,
            ],

            'start_date' => $this->start_date?->format('Y-m-d'),
            'end_date' => $this->end_date?->format('Y-m-d'),
            'days_count' => $this->days_count,
            'status' => $this->status,

            'manager_approval_status'
                => $this->manager_approval_status,

            'hr_approval_status'
                => $this->hr_approval_status,

            'reason' => $this->reason,

            'rejection_reason'
                => $this->rejection_reason,

            'attachment_path'
                => $this->attachment_path,

           'created_at' 
               => $this->created_at?->toDateTimeString(),
           'updated_at'
               => $this->updated_at?->toDateTimeString(),
        ];
    }
}
 