<?php

namespace Modules\Attendance\DTOs;

use Modules\Attendance\Http\Requests\FilterAttendanceLogRequest;

class FilterAttendanceLogDTO
{
    public function __construct(
        public readonly ?int $employeeId = null,
        public readonly ?string $type = null,
        public readonly ?string $result = null,
        public readonly ?string $message = null,
        public readonly ?string $fromDate = null,
        public readonly ?string $toDate = null,
        public readonly ?int $perPage = 10
    ) {}


    public static function fromRequest(FilterAttendanceLogRequest $request): self
    {
        $validated = $request->validated();

        return new self(
            employeeId: isset($validated['employee_id'])
                ? (int) $validated['employee_id']
                : null,

            type: $validated['type'] ?? null,

            result: $validated['result'] ?? null,

            fromDate: $validated['from_date'] ?? null,

            toDate: $validated['to_date'] ?? null,

            perPage: isset($validated['per_page'])
                ? (int) $validated['per_page']
                : 10
        );
    }


    public function toArray(): array
    {
        return array_filter([
            'employee_id' => $this->employeeId,
            'type' => $this->type,
            'result' => $this->result,
            'from_date' => $this->fromDate,
            'to_date' => $this->toDate,
            'per_page' => $this->perPage,
        ], fn ($value) => !is_null($value));
    }
}