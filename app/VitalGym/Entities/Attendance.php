<?php

namespace App\VitalGym\Entities;

use App\Traits\PerPageTrait;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use PerPageTrait;

    protected $guarded = [];

    protected $casts = [
        'date' => 'datetime',
    ];

    protected $dates = ['date'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
