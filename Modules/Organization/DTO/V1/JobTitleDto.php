<?php

namespace Modules\Organization\DTO\V1;

class JobTitleDTO
{
    public function __construct(
        public readonly string $title,
        public readonly string $grade,
        public readonly int $departmentId,
        public readonly ?string $description = null,
        public readonly ?bool $isActive = true,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            title: $data['title'],
            grade: $data['grade'],
            departmentId: (int) $data['department_id'],
            description: $data['description'] ?? null,
            isActive: isset($data['is_active']) ? (bool) $data['is_active'] : true,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'title' => $this->title,
            'grade' => $this->grade,
            'department_id' => $this->departmentId,
            'description' => $this->description,
            'is_active' => $this->isActive,
        ], fn ($value) => $value !== null);
    }
}
