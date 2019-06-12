<?php

namespace App\VitalGym\Entities;

use App\Traits\PerPageTrait;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use PerPageTrait;

    protected $guarded = [];

    protected $casts = [
      'is_premium' => 'boolean',
    ];

    public function getPriceInDollarsAttribute()
    {
        return number_format($this->price / 100, 2);
    }

    public function memberships()
    {
        return $this->hasMany(Membership::class);
    }
}
