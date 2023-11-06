<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductQrcode extends Model
{
    protected $fillable = [
        'product_id', 'variation_id', 'running_code', 'status'
    ];
}
