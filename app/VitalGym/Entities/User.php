<?php

namespace App\VitalGym\Entities;

use App\Filters\UserFilter;
use App\Traits\PerPageTrait;
use App\Filters\Traits\Filterable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable, SoftDeletes, Filterable, PerPageTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    protected $filters = UserFilter::class;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'api_token',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function token()
    {
        return $this->hasOne(ActivationToken::class);
    }

    public function customer()
    {
        return $this->hasOne(Customer::class);
    }

    public function getStatusAttribute()
    {
        return $this->attributes['active'] ? 'activo' : 'inactivo';
    }

    public function getFullNameAttribute()
    {
        return $this->attributes['name'].' '.$this->attributes['last_name'];
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public static function createWithActivationToken($data)
    {
        $user = self::create($data);
        $user->token()->create(['token' => str_random(60)]);

        return $user;
    }
}
