<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductVariation extends Model
{
    protected $fillable = [
        'product_id', 'variation_name', 'variation_price', 'variation_special_price', 'variation_agent_price', 'variation_agent_special_price', 'variation_sku', 'status', 
        'variation_point_price', 'variation_point_agent_price', 'variation_get_point_price', 'variation_get_point_agent_price', 'variation_weight',
        'variation_chinese_name', 	
    ];
}
