<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'guest_agent', 'transaction_no', 'user_id', 'discount_code', 'discount', 'processing_fee', 'tax', 'shipping_fee', 'grand_total', 
        'sub_total', 'address_name', 'address', 'postcode', 'city', 'state', 'phone', 'email', 'mall', 'bank_id', 'cdm_bank_id', 
        'bank_slip', 'bank_slip_no', 'bank_slip_date', 'status', 'completed', 'rebate_amount', 'awb_no',
        'online_payment_method', 'bank_name', 'card_holder_name', 'card_mask', 'card_exp', 'card_type', 'additional_shipping_fee'
    ];
}
