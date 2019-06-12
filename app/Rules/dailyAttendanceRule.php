<?php

namespace App\Rules;

use App\VitalGym\Entities\Attendance;
use Illuminate\Contracts\Validation\Rule;

class dailyAttendanceRule implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $customerId
     * @return bool
     */
    public function passes($attribute, $customerId)
    {
        return Attendance::whereCustomerId($customerId)->whereDay('date', now()->day)->count() === 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.daily_membership');
    }
}
