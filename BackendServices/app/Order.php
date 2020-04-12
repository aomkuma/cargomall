<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    //
    protected $table = 'order';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id', 'user_id', 'order_no', 'tracking_no', 'tracking_no_thai', 'import_fee', 'net_price', 'estimate_cost', 'actual_cost', 'customer_return_actual_cost', 'discount', 'transport_type', 'payment_type', 'product_type', 'receive_order_type', 'customer_address_id', 'transport_company', 'transport_company_other', 'add_on', 'check_use_cash_balance', 'use_cash_balance', 'order_status', 'china_arrival_date'
            , 'cargo', 'package_amount', 'weight_kg', 'longs', 'widths', 'heights', 'cbm', 'china_departure_date', 'bill_no'
            , 'created_at', 'updated_at'
    ];

    public function totalYuan()
    {
        return $this->hasOne('App\OrderDetail','order_id','id');
    }

    public function orderDesc()
    {
        return $this->hasOne('App\OrderDesc','order_id','id');
    }

    public function orderDetails()
    {
        return $this->hasMany('App\OrderDetail','order_id','id');
    }

    public function customer()
    {
        return $this->hasOne('App\User','id','user_id');
    }

    public function customerAddress()
    {
        return $this->hasOne('App\UserAddress','id','user_id');
    }

    public function orderTrackings()
    {
        return $this->hasMany('App\OrderTracking','order_id','id');
    }
}
