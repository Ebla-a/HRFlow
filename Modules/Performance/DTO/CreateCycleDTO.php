<?php
namespace Modules\Performance\DTO;

use Illuminate\Support\Facades\Date;


readonly class CreateCycleDTO
{
    public function __construct(
        public readonly string $name,
        public readonly Date $start_date,
        public readonly Date $end_date,
    ) {}

    public static function fromRequest(array $validated): self
    {
        return new self(
            name: $validated['name'],
            start_date: $validated['start_date'],
            end_date: $validated['end_date'],
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'name'      => $this->name,
            'start_date' => $this->start_date,
            'end_date'   => $this->end_date,
        ], fn ($value) => $value !== null);
    }
}