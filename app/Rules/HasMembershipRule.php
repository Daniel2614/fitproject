<?php

namespace App\Rules;

use App\VitalGym\Entities\Customer;
use Illuminate\Contracts\Validation\Rule;

class HasMembershipRule implements Rule
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
        $customer = Customer::findOrFail($customerId);

        return (bool) $customer->memberships()->count() > 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.has_membership');
    }
}
