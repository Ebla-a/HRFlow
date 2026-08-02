<?php
namespace Modules\Performance\Rules;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Filter implements ValidationRule
{

    protected array $badWords = [
        'badword1',
        'badword2',
        
    ];

    /**
     * Determine if the validation rule passes.
    */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            return;
        }

        $lowercaseValue = mb_strtolower($value);

        foreach ($this->badWords as $badWord) {
            if (str_contains($lowercaseValue, mb_strtolower($badWord))) {
                $fail("{$attribute}.filter");
                return;
            }
        }
    }
}