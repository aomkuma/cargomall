<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderTracking extends Model
{
    //
    protected $table = 'order_tracking';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
	public $incrementing = false;
    
    protected $fillable = [
        'id', 'user_id', 'order_id', 'order_no', 'tracking_no', 'tracking_no_thai', 'import_fee', 'net_price', 'estimate_cost', 'actual_cost', 'customer_return_actual_cost', 'discount', 'transport_type', 'payment_type', 'product_type', 'receive_order_type', 'customer_address_id', 'transport_company', 'transport_company_other', 'add_on', 'check_use_cash_balance', 'use_cash_balance', 'order_status', 'china_arrival_date'
            , 'cargo', 'package_amount', 'weight_kg', 'longs', 'widths', 'heights', 'cbm', 'china_departure_date', 'bill_no'
            ,'transport_cost_china', 'transport_cost_thai', 'payment_status', 'created_at', 'updated_at'
    ];

    protected $casts = [
        
        'import_fee' => 'float'
    ];

}
