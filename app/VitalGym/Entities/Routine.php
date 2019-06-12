<?php

namespace App\VitalGym\Entities;

use App\Filters\LevelFilter;
use App\Traits\PerPageTrait;
use App\Filters\Traits\Filterable;
use Illuminate\Database\Eloquent\Model;

class Routine extends Model
{
    use Filterable, PerPageTrait;

    protected $fillable = [
        'level_id', 'name', 'file', 'description',
    ];

    protected $filters = LevelFilter::class;

    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }
}
