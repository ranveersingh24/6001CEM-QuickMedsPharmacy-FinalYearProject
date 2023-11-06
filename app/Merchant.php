<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Merchant extends Authenticatable
{
    use Notifiable;
    protected $guard = 'merchant';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
     protected $fillable = [
        'master_id', 'code', 'email', 'password', 'f_name', 'l_name', 'gender', 'dob', 'phone', 'point', 'lvl', 'permission_lvl', 'profile_logo', 'agent_type', 'status', 'maintain_admin'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
}
