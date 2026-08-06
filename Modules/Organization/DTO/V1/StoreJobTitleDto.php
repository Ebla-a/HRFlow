<?php

namespace Modules\Organization\DTO\V1;

use Modules\Organization\Enums\JobTitleGrade;

class StoreJobTitleDto
{
    public function __construct(
        public readonly string $title,
        public readonly int $departmentId,
        public readonly ?JobTitleGrade $grade = null,
        public readonly ?string $description = null,
        public readonly bool $isActive = true,
    ) {}

    /**
     * Summary of fromRequest
     * @param array $data
     * @return StoreJobTitleDto
     */
    public static function fromRequest(array $data): self
    {
        $gradeValue = isset($data['grade'])
            ? ($data['grade'] instanceof JobTitleGrade ? $data['grade'] : JobTitleGrade::tryFrom($data['grade']))
            : null;

        return new self(
            title: $data['title'],
            departmentId: (int) $data['department_id'],
            grade: $gradeValue,
            description: $data['description'] ?? null,
            isActive: isset($data['is_active']) ? (bool) $data['is_active'] : true,
        );
    }

    /**
     * Summary of toArray
     * @return array{department_id: int, description: string|null, grade: string|null, is_active: bool, title: string}
     */
    public function toArray(): array
    {
        return array_filter([
            'title'         => $this->title,
            'department_id' => $this->departmentId,
            'grade'         => $this->grade?->value,
            'description'   => $this->description,
            'is_active'     => $this->isActive,
        ], fn ($value) => $value !== null);
    }
}
