<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WebsiteSetting extends Model
{
    protected $fillable = [
        'address', 'about_us_image','contact_us', 'contact_us_image'
    ];
}
