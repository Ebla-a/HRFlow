<?php

namespace Modules\Attendance\DTOs;

use Modules\Attendance\Http\Requests\CheckAttendanceRequest;

class CheckAttendanceDTO
{
    public function __construct(
        public readonly int $employeeId,
        public readonly string $type
    ) {}

    public static function fromRequest(CheckAttendanceRequest $request): self
    {
        $validated = $request->validated();

        return new self(
            employeeId: (int) $validated['employee_id'],
            type: $validated['type']
        );
    }

    public function toArray(): array
    {
        return [
            'employee_id' => $this->employeeId,
            'type' => $this->type,
        ];
    }
}