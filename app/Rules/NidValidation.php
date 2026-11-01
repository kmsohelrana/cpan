<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NidValidation implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value === null || $value === '') {
            return;
        }

        $value = (string) $value;

        if (preg_match('/\A\d+\z/', $value) !== 1) {
            $fail('validation.invalid_nid_numeric')->translate();

            return;
        }

        if (!in_array(strlen($value), [10, 13, 17])) {
            $fail('validation.invalid_nid')->translate();
        }
    }
}
