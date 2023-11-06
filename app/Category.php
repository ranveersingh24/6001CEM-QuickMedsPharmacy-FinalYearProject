<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'menu_bar', 'code', 'category_name', 'status','category_chinese_name',
    ];
}
