<?php

namespace App\Rules;

use App\VitalGym\Entities\Customer;
use Illuminate\Contracts\Validation\Rule;

class ActiveMembershipRule implements Rule
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

        return $customer->memberships->last()->date_end->isToday()
               || $customer->memberships->last()->date_end >= today();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.active_membership');
    }
}
