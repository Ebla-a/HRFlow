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

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($this->data['start_date'])) {
            return;
        }

        if (!strtotime($this->data['start_date']) || !strtotime($value)) {
            return;
        }
        
        $start = Carbon::parse($this->data['start_date']);
        $end   = Carbon::parse($value);

  
        if ($end->lessThan($start->copy()->addDays(3))) {
            $fail('The end date must be at least 3 days after the start date.');
        }
    }
}