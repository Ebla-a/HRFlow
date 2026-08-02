<?php

namespace Modules\Leave\DTO;

use Modules\Leave\Http\Requests\StoreLeaveRequestRequest;

class LeaveRequestDTO
{
    public function __construct(
        public readonly int $leave_type_id,
        public readonly string $start_date,
        public readonly string $end_date,
        public readonly ?string $reason = null,
        public readonly ?int $employee_id = null,
    ) {
    }

    public static function fromRequest(StoreLeaveRequestRequest $request): self
    { 
        return new self(
            leave_type_id: (int) $request->validated('leave_type_id'),
            start_date: $request->validated('start_date'),
            end_date: $request->validated('end_date'),
            reason: $request->validated('reason'),
            employee_id: $request->validated('employee_id'),
        );
    }
 
    public function toArray(): array
    {
        return array_filter([
            'leave_type_id' => $this->leave_type_id,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'reason' => $this->reason,
            'employee_id' => $this->employee_id,
        ], fn ($value) => ! is_null($value));
    }
} 