<?php

namespace App\VitalGym\Entities;

use App\Traits\PerPageTrait;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use PerPageTrait;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function membership()
    {
        return $this->hasOne(Membership::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function getTotalPriceInDollarsAttribute()
    {
        return number_format($this->total_price / 100, 2);
    }
}
