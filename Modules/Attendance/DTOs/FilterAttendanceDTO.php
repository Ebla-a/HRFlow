<?php

namespace Modules\Attendance\DTOs;

use Modules\Attendance\Http\Requests\FilterAttendanceRequest;

class FilterAttendanceDTO
{
    public function __construct(
        public readonly ?int $employeeId = null,
        public readonly ?string $status = null,
        public readonly ?string $fromDate = null,
        public readonly ?string $toDate = null,
        public readonly ?bool $late = null,
        public readonly ?string $sortBy = null,
        public readonly ?int $perPage = 10
    ) {}

    public static function fromRequest(FilterAttendanceRequest $request): self
    {
        $validated = $request->validated();

        return new self(
            employeeId: isset($validated['employee_id']) ? (int) $validated['employee_id'] : null,
            status: $validated['status'] ?? null,
            fromDate: $validated['from_date'] ?? null,
            toDate: $validated['to_date'] ?? null,
            late: isset($validated['late']) ? (bool) $validated['late'] : null,
            sortBy: $validated['sort_by'] ?? null,
            perPage: isset($validated['per_page']) ? (int) $validated['per_page'] : 10
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'employee_id' => $this->employeeId,
            'status' => $this->status,
            'from_date' => $this->fromDate,
            'to_date' => $this->toDate,
            'late' => $this->late,
            'sort_by' => $this->sortBy,
            'per_page' => $this->perPage,
        ], fn ($value) => !is_null($value));
    }
}