<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MoneyUse extends Model
{
    //
    //
    protected $table = 'money_use';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s','modified' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s','modified' => 'datetime:Y-m-d H:i:s',
    ];
    
    protected $fillable = [
        'id', 'user_id', 'pay_amount_yuan', 'pay_amount_thb', 'exchange_rate', 'remark', 'pay_type', 'pay_status', 'to_ref_id', 'to_ref_id_2', 'order_no', 'payer_name', 'to_bank_acc_no', 'to_bank_name', 'alipay', 'slip_path', 'created_at', 'updated_at'
    ];

    public function customer()
    {
        return $this->hasOne('App\User','id','user_id');
    }

    public function importer()
    {
        return $this->hasOne('App\Importer','id','to_ref_id');
    }

    public function order()
    {
        return $this->hasOne('App\Order','id','to_ref_id');
    }

    public function orderTracking()
    {
        return $this->hasOne('App\OrderTracking','tracking_no','to_ref_id_2');
    }
}
