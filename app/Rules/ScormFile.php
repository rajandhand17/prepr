<?php

namespace App\Rules;

use App\Exceptions\Scrom\InvalidScormArchiveException;
use App\Services\Manage\Scorm\Utils\ScormArchiver;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ScormFile implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try {
            app()->make(ScormArchiver::class)->parseScormArchive($value);
        } catch (InvalidScormArchiveException $e) {
            $fail('The file uploaded is not a valid scorm file.');
        }
    }
}
