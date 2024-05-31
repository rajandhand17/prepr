<?php

namespace App\Rules;

use App\Helpers\CryptHelper;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class UnifiedStateRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $decrypted = CryptHelper::decrypt($value);
        if ($decrypted === false) {
            $fail(__('responses.unified_state_invalid'));
        }
    }
}
