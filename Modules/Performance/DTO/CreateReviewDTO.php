<?php

namespace Modules\Performance\DTO;

use Illuminate\Support\Facades\Auth;

readonly class CreateReviewDTO
{
    public function __construct(
        public readonly int $employee_id,
        public readonly int $performance_cycle_id,
        public readonly int $reviewer_id,
        public readonly string $comments,
        public readonly int $score,
    ) {}

    public static function fromRequest(array $validated): self
    {
        return new self(
            employee_id: (int) $validated['employee_id'],
            performance_cycle_id: (int) $validated['performance_cycle_id'],
            reviewer_id: (int) ($validated['reviewer_id'] ?? Auth::user()->employee?->id),
            comments: $validated['comments'],
            score: (int) $validated['score'],
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'employee_id'          => $this->employee_id,
            'performance_cycle_id' => $this->performance_cycle_id,
            'reviewer_id'          => $this->reviewer_id,
            'comments'             => $this->comments,
            'score'                => $this->score,
        ], fn ($value) => $value !== null);
    }
}