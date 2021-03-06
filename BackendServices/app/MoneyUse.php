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
    
    protected $fillable = [
        'id', 'user_id', 'pay_amount_yuan', 'pay_amount_thb', 'exchange_rate', 'remark', 'pay_type', 'pay_status', 'to_ref_id', 'to_ref_id_2', 'order_no', 'payer_name', 'to_bank_acc_no', 'to_bank_name', 'alipay', 'created_at', 'updated_at'
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
