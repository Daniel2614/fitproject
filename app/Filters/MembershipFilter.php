<?php

namespace App\Filters;

use App\VitalGym\Entities\User;
use App\VitalGym\Entities\Customer;

class MembershipFilter extends Filter
{
    public function name($name)
    {
        $userIds = User::with('level')->where('name', 'like', "%{$name}%")->pluck('id');
        $customerIds = Customer::whereIn('user_id', $userIds)->pluck('id');

        return $this->builder->whereIn('customer_id', $customerIds);
    }

    public function email($email)
    {
        $userIds = User::whereRole('customer')->where('email', 'like', "%{$email}%")->pluck('id');
        $customerIds = Customer::whereIn('user_id', $userIds)->pluck('id');

        return $this->builder->whereIn('customer_id', $customerIds);
    }
}
