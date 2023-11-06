<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VariationStock extends Model
{
    protected $fillable = [
        'variation_id', 'type', 'quantity', 'remark', 'status'
    ];
}
