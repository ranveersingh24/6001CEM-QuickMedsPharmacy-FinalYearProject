<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SettingShippingFee extends Model
{
    protected $fillable = [
        'area', 'weight', 'shipping_fee', 'status'
    ];
}
