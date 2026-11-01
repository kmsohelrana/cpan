<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Request;

class ValidateMobileNumber implements ValidationRule
{
    protected array $exceptConditions;

    /**
     * Create a new rule instance.
     */
    public function __construct(array $exceptConditions = [])
    {
        $this->exceptConditions = $exceptConditions;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Skip validation if value is null or empty (for nullable fields)
        if (empty($value)) {
            return;
        }

        // Access the current request data
        $requestData = Request::all();

        // Check if the validation should be skipped
        foreach ($this->exceptConditions as $column => $values) {
            if (isset($requestData[$column]) && in_array($requestData[$column], (array) $values)) {
                return; // Skip the validation
            }
        }

        // Regex pattern for validating mobile numbers
        if (! preg_match('/^01[3-9][0-9]{8}$/', $value)) {
            $fail('validation.invalid_mobile_number')->translate();
        }
    }
}
