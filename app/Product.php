<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'featured', 'packages', 'item_code', 'product_code',  'product_name','sold_count' ,'category_id', 'sub_category_id', 'brand_id', 
        'price', 'special_price', 'agent_price', 'agent_special_price', 'variation_enable', 'free_gift', 'quantity', 'description', 
        'short_description', 'mall', 'status', 'product_type', 'f_banner', 's_banner', 'weight', 'product_comm_type', 'product_comm_amount', 'in_product_comm_type', 'in_product_comm_amount', 'own_product_comm_type', 
        'own_product_comm_amount', 'point_price', 'point_agent_price', 'get_point_price', 'get_point_agent_price', 'chinese_name','chinese_description',
    ];
}
