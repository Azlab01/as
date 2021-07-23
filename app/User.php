<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'code', 'password', 'service_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function astreintes()
    {
        return $this->belongsToMany(Astreinte::class);
    }

    public function conges()
    {
        return $this->belongsToMany(Astreinte::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function presence()
    {
        return $this->hasMany(Presence::class);
    }
}
