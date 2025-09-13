<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class BouestiEmail implements ValidationRule
{
    protected string $type;

    public function __construct(string $type = 'student')
    {
        $this->type = $type;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($this->type === 'student') {
            // Student email pattern: firstname.matricno@bouesti.edu.ng
            if (! preg_match('/^[a-z]+\.[0-9]+@bouesti\.edu\.ng$/i', $value)) {
                $fail('The :attribute must be a valid BOUESTI student email (firstname.matricno@bouesti.edu.ng).');
            }
        } else {
            // Staff email pattern: firstname.lastname@bouesti.edu.ng
            if (! preg_match('/^[a-z]+\.[a-z]+@bouesti\.edu\.ng$/i', $value)) {
                $fail('The :attribute must be a valid BOUESTI staff email (firstname.lastname@bouesti.edu.ng).');
            }
        }
    }
}
