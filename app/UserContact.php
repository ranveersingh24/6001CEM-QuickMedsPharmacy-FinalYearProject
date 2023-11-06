<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserContact extends Model
{
    

     protected $fillable = [
        'user_name', 'user_mail', 'user_feedback'
    ];

    
}
