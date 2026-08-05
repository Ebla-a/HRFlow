<?php
namespace Modules\Performance\DTO;

use Illuminate\Support\Facades\Date;
use Illuminate\Support\Carbon;

readonly class CreateCycleDTO
{
    public function __construct(
        public readonly string $name,
        public readonly Carbon $start_date,
        public readonly Carbon $end_date,
    ) {}

    public static function fromRequest(array $validated): self
    {
        return new self(
            name: $validated['name'],
            start_date: Date::parse($validated['start_date']),
            end_date:   Date::parse($validated['end_date']),
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'name'      => $this->name,
            'start_date' => $this->start_date->toDateString(),
            'end_date'   => $this->end_date->toDateString(),
        ], fn ($value) => $value !== null);
    }
}