<?php
namespace Modules\Performance\DTO;


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
            employee_id: $validated['employee_id'],
            performance_cycle_id: $validated['performance_cycle_id'],
            reviewer_id: $validated['reviewer_id'],
            comments: $validated['comments'],
            score: $validated['score'],
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'employee_id' => $this->employee_id,
            'performance_cycle_id' => $this->performance_cycle_id,
            'reviewer_id' => $this->reviewer_id,
            'comments' => $this->comments,
            'score' => $this->score,
        ], fn ($value) => $value !== null);
    }
}