<?php

namespace App\VitalGym\Entities;

use App\Traits\PerPageTrait;
use App\Filters\MembershipFilter;
use App\Filters\Traits\Filterable;
use Illuminate\Database\Eloquent\Model;

class Membership extends Model
{
    use Filterable, PerPageTrait;

    protected $guarded = [];

    protected $dates = ['date_start', 'date_end'];

    protected $filters = MembershipFilter::class;

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function scopeExpired($query)
    {
        return $query->whereDate('date_end', now()->today());
    }
}
