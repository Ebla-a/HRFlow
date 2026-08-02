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
        public readonly mixed $attachment = null,
        public readonly ?int $employee_id = null
    ) {}

    /**
     * Factory method to build DTO directly from FormRequest
     */
    public static function fromRequest(StoreLeaveRequestRequest $request): self
    {
        // التعامل مع رفح الملف المرفق وحفظ مساره
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('leave_attachments', 'public');
        }

        return new self(
            leave_type_id: (int) $request->validated('leave_type_id'),
            start_date:    (string) $request->validated('start_date'),
            end_date:      (string) $request->validated('end_date'),
            reason:        $request->validated('reason'),
            attachment:    $attachmentPath,
            employee_id:   $request->validated('employee_id') ? (int) $request->validated('employee_id') : null
        );
    }

    /**
     * Convert DTO to Array
     */
    public function toArray(): array
    {
        return array_filter([
            'leave_type_id' => $this->leave_type_id,
            'start_date'    => $this->start_date,
            'end_date'      => $this->end_date,
            'reason'        => $this->reason,
            'attachment_path'    => $this->attachment,
            'employee_id'   => $this->employee_id,
        ], fn ($value) => !is_null($value));
    }
}
 