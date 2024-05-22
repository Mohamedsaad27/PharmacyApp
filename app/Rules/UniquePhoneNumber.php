<?php

namespace App\Rules;

use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Pharmacy;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UniquePhoneNumber implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$this->passes($attribute, $value)) {
            $fail('The phone number has already been taken.');
        }
    }
    public function passes($attribute, $value)
    {
        $doctorExists = Doctor::where('phone_number', $value)->exists();
        $patientExists = Patient::where('phone_number', $value)->exists();
        $pharmacyExists = Pharmacy::where('phone_number', $value)->exists();

        return !$doctorExists && !$patientExists && !$pharmacyExists;
    }

    public function message()
    {
        return 'The phone number has already been taken.';
    }
}
