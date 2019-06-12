<?php

namespace App\Filters;

class UserFilter extends Filter
{
    public function filter($value)
    {
        return $this->builder
            ->where('name', 'like', '%'.$value.'%')
            ->orWhere('last_name', 'like', '%'.$value.'%')
            ->orWhere('nick_name', 'like', '%'.$value.'%')
            ->orWhere('email', 'like', '%'.$value.'%');
    }
}
