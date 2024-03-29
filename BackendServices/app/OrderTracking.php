<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use DateTimeInterface;

class OrderTracking extends Model
{
    //
    protected $table = 'order_tracking';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
	public $incrementing = false;

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s','modified' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s','modified' => 'datetime:Y-m-d H:i:s',
        // 'china_arrival_date' => 'datetime:Y-m-d H:i:s','modified' => 'datetime:Y-m-d H:i:s',
        // 'china_departure_date' => 'datetime:Y-m-d H:i:s','modified' => 'datetime:Y-m-d H:i:s',
        // 'thai_arrival_date' => 'datetime:Y-m-d H:i:s','modified' => 'datetime:Y-m-d H:i:s',
        'import_fee' => 'float',
        'is_tracking_none_owner' => 'bool'
    ];
    
    protected $fillable = [
        'id', 'user_id', 'order_id', 'order_no','china_order_no', 'china_tracking_no', 'tracking_no', 'tracking_no_thai', 'import_fee', 'net_price', 'estimate_cost', 'actual_cost', 'customer_return_actual_cost', 'discount', 'transport_type', 'payment_type', 'product_type', 'receive_order_type', 'customer_address_id', 'transport_company', 'transport_company_other', 'add_on', 'check_use_cash_balance', 'use_cash_balance', 'order_status', 'china_arrival_date'
            , 'cargo', 'package_amount', 'weight_kg', 'longs', 'widths', 'heights', 'cbm', 'china_departure_date', 'bill_no'
            ,'transport_cost_china', 'transport_cost_thai', 'goods_desc_th', 'goods_desc_en', 'thai_arrival_date', 'container_no', 'payment_status', 'is_tracking_none_owner', 'created_at', 'updated_at'
    ];


    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

}
