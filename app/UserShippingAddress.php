<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserShippingAddress extends Model
{
    protected $fillable = [
        'user_id', 'default','f_name', 'l_name', 'email', 'phone', 'address', 'country', 'state', 'city', 'postcode', 'status', 'created_at', 'updated_at'
    ];
}
