<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    use Notifiable;

    protected $guard = 'admin';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
     protected $fillable = [
        'code', 'email', 'password', 'f_name', 'l_name', 'gender', 'dob', 'phone', 'logo_hidden', 'website_logo', 'name_hidden', 'website_name', 'lvl', 'permission_lvl', 'profile_logo', 'contact_email'
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
