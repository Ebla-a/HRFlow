<?php

namespace Modules\Attendance\DTOs;

use Modules\Attendance\Http\Requests\UpdateAttendanceRequest;

class UpdateAttendanceDTO
{
    public function __construct(
        public readonly ?string $checkIn = null,
        public readonly ?string $checkOut = null,
        public readonly ?string $status = null,
        public readonly ?string $notes = null
    ) {}

    public static function fromRequest(UpdateAttendanceRequest $request): self
    {
        $validated = $request->validated();

        return new self(
            checkIn: $validated['check_in'] ?? null,
            checkOut: $validated['check_out'] ?? null,
            status: $validated['status'] ?? null,
            notes: $validated['notes'] ?? null
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'check_in' => $this->checkIn,
            'check_out' => $this->checkOut,
            'status' => $this->status,
            'notes' => $this->notes,
        ], fn ($value) => !is_null($value));
    }
}