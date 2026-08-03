<?php

namespace Modules\Performance\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\DataAwareRule;
use Carbon\Carbon;

class MinimumDaysAfter implements ValidationRule, DataAwareRule
{

    protected array $data = [];

    public function setData(array $data): static
    {
        $this->data = $data;
        return $this;
    }
    /**
     * Determine if the validation rule passes.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!isset($this->data['start_date'])) {
            return;
        }
        $startDate = $this->data['start_date'];

        $start = Carbon::parse($startDate);
        $end   = Carbon::parse($value);

        if ($end->diffInDays($start) < 3) {
            $fail('end_date.minimum_days_after');
        }
    }
}
