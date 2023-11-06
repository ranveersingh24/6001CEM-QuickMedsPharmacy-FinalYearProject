<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = [
        'mall', 'user_id', 'product_id', 'sub_category_id', 'second_sub_category_id', 'qty', 'status'
    ];
}
