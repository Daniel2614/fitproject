<?php

namespace App\VitalGym\Entities;

use App\Traits\PerPageTrait;
use App\Filters\CustomerFilter;
use App\Filters\Traits\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Customer extends Model
{
    use Filterable, PerPageTrait, Notifiable;

    protected $filters = CustomerFilter::class;

    protected $guarded = [];

    protected $dates = ['birthdate'];

    protected $with = ['user'];

    public function Memberships()
    {
        return $this->hasMany(Membership::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function routine()
    {
        return $this->belongsTo(Routine::class);
    }

    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function getFullNameAttribute()
    {
        return "{$this->user->name} {$this->user->last_name}";
    }

    public function getEmailAttribute()
    {
        return $this->user->email;
    }

    public function getNameAttribute()
    {
        return $this->user->name;
    }

    public function getAvatarAttribute()
    {
        return $this->user->avatar;
    }

    public function getLastNameAttribute()
    {
        return $this->user->last_name;
    }

    public function getPhoneAttribute()
    {
        return $this->user->phone;
    }

    public function getAddressAttribute()
    {
        return $this->user->address;
    }

    public function getCellPhoneAttribute()
    {
        return $this->user->cell_phone;
    }

    public static function membershipExpiredToday()
    {
        $memberships = Membership::with('customer')->expired()->get();

        return $memberships->map(function ($membership) {
            return $membership->customer;
        })->unique();
    }
}
