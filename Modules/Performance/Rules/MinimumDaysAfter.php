<?php

namespace Modules\Performance\Rules;

use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;

class MinimumDaysAfter implements ValidationRule, DataAwareRule
{
    protected array $data = [];

    public function __construct(
        protected int $minDays = 3
    ) {}

    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    public function validate(
        string $attribute,
        mixed $value,
        Closure $fail
    ): void {
        if (empty($this->data['start_date'])) {
            return;
        }

        if (
            !strtotime($this->data['start_date']) ||
            !strtotime($value)
        ) {
            return;
        }

        $start = Carbon::parse($this->data['start_date']);
        $end = Carbon::parse($value);

        if (
            $end->isBefore($start) ||
            $start->diffInDays($end) < $this->minDays
        ) {
            $fail(
                "The {$attribute} must be at least {$this->minDays} days after the start date."
            );
        }
    }
}