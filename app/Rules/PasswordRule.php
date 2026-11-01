<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class PasswordRule implements Rule
{
    private $message;
    private $min;
    private $mixedCase;
    private $letters;
    private $numbers;
    private $symbols;
    private $sequentialCharacters;
    private $repeatedCharacters;
    private $blockLast3Passwords;

    public function __construct($min = 8, $mixedCase = true, $letters = true, $numbers = true, $symbols = true, $sequentialCharacters = false, $repeatedCharacters = false, $blockLast3Passwords = true)
    {
        $this->min = $min;
        $this->mixedCase = $mixedCase;
        $this->letters = $letters;
        $this->numbers = $numbers;
        $this->symbols = $symbols;
        $this->sequentialCharacters = $sequentialCharacters;
        $this->repeatedCharacters = $repeatedCharacters;
        $this->blockLast3Passwords = $blockLast3Passwords;
    }

    public function passes($attribute, $value)
    {
        if (!is_string($value)) {
            $this->message = 'The :attribute must be a string.';
            return false;
        }

        $value = (string)$value;

        if (!(mb_strlen($value) >= $this->min)) {
            $this->message = 'The :attribute must be at least ' . $this->min . ' characters.';
            return false;
        }

        if ($this->mixedCase && !preg_match('/(\p{Ll}+.*\p{Lu})|(\p{Lu}+.*\p{Ll})/u', $value)) {
            $this->message = 'The :attribute must contain at least one uppercase and one lowercase letter.';
            return false;
        }

        if ($this->letters && !preg_match('/\pL/u', $value)) {
            $this->message = 'The :attribute must contain at least one letter.';
            return false;
        }

        if (!$this->repeatedCharacters && !preg_match('/^(?!.*(.)\1\1).*$/', $value)) {
            $this->message = 'The :attribute cannot have more than 2 repeated characters consecutively.';
            return false;
        }

        if (!$this->sequentialCharacters && !preg_match('/^(?!.*(123|234|345|456|567|678|789|890|abc|bcd|cde|def|efg|fgh|ghi|hij|ijk|jkl|klm|lmn|mno|nop|opq|pqr|qrs|rst|stu|tuv|uvw|vwx|wxy|xyz)).*$/i', $value)) {
            $this->message = 'The :attribute cannot contain sequential or serial characters.';
            return false;
        }

        if ($this->symbols && !preg_match('/\p{Z}|\p{S}|\p{P}/u', $value)) {
            $this->message = 'The :attribute must contain at least one symbol.';
            return false;
        }

        if ($this->symbols && !preg_match('/^[a-zA-Z0-9#@$%&*()]+$/', $value)) {
            $this->message = 'The :attribute must contain only valid symbols: #@$%&*()';
            return false;
        }

        if ($this->numbers && !preg_match('/\pN/u', $value)) {
            $this->message = 'The :attribute must contain at least one number.';
            return false;
        }

        if ($this->blockLast3Passwords && auth()->check()) {
            $passwords = auth()->user()->password_history ?? [];
            foreach ($passwords as $password) {
                if (Hash::check($value, $password)) {
                    $this->message = 'The :attribute cannot be the same as any of your last 3 passwords.';
                    return false;
                }
            }
        }

        return true;
    }

    public function message()
    {
        return $this->message;
    }
}
