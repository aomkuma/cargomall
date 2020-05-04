<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderDesc extends Model
{
    //
    protected $table = 'order_desc';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
	public $incrementing = false;
    
    protected $fillable = [
        'id', 'order_id', 'sum_line_baht', 'china_transport_cost', 'china_ex_rate', 'total_china_transport_cost', 'sum_add_on1', 'sum_add_on2', 'sum_add_on', 'price_by_kgm', 'price_by_cbm', 'total_prod_price', 'transport_company_cost', 'china_thai_transport_cost', 'prod_type_id', 'width', 'longs', 'height', 'metrics', 'weight_kgm', 'remark', 'created_at', 'updated_at'
    ];
}
