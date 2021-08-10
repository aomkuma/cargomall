<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use DateTimeInterface;

class Importer extends Model
{
    //
    protected $table = 'importer';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s','modified' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s','modified' => 'datetime:Y-m-d H:i:s',
        // 'china_arrival' => 'datetime:Y.m.d','modified' => 'datetime:Y-m-d H:i:s',
        // 'china_departure' => 'datetime:Y.m.d','modified' => 'datetime:Y-m-d H:i:s',
        // 'thai_arrival' => 'datetime:Y.m.d','modified' => 'datetime:Y-m-d H:i:s',
        // 'thai_departure' => 'datetime:Y.m.d','modified' => 'datetime:Y-m-d H:i:s',
    ];
    
    protected $fillable = [
        'id', 'user_id', 'customer_address_id', 'tracking_no', 'tracking_no_thai', 'bill_no', 'warehouse', 'width', 'longs', 'height', 'weight_kgm', 'weight_volume', 'cbm', 'transport_type', 'product_type', 'china_arrival', 'china_departure', 'thai_arrival', 'thai_departure', 'price_method', 'total_price_thb', 'total_price_yuan', 'package_price', 'discount', 'package_amount', 'transport_company', 'transport_company_other', 'remark', 'remark_customer',
            'container_no',
         'importer_status', 'created_at', 'updated_at'
    ];

    public function customer()
    {
        return $this->hasOne('App\User','id','user_id');
    }

    public function customerAddress()
    {
        return $this->hasOne('App\UserAddress','id','customer_address_id');
    }
    
     protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
