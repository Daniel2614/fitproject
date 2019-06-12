<?php

namespace App\VitalGym\Entities;

use App\Traits\PerPageTrait;
use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    use PerPageTrait;

    protected $fillable = ['name'];

    public function routines()
    {
        return $this->hasMany(Routine::class);
    }

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }
}
