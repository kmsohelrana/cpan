<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ContainsPlaceholders implements ValidationRule
{
    public function __construct(private array $placeholders) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value)) {
            return;
        }

        $missing = array_filter($this->placeholders, fn($p) => !str_contains($value, $p));

        if (!empty($missing)) {
            $fail('The :attribute must contain the required placeholder(s): ' . implode(', ', $missing) . '.');
        }
    }
}
