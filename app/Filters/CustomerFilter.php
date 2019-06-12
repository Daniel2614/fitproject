<?php

namespace App\Filters;

use App\VitalGym\Entities\User;

class CustomerFilter extends Filter
{
    public function ci($value)
    {
        return $this->builder->where('ci', 'like', "{$value}%");
    }

    public function email($value)
    {
        $userIds = User::where('email', 'like', "%{$value}%")->pluck('id');

        return $this->builder->whereIn('user_id', $userIds);
    }
}
