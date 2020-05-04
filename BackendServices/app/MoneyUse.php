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
        'id', 'user_id', 'pay_amount_yuan', 'pay_amount_thb', 'exchange_rate', 'remark', 'pay_type', 'pay_status', 'to_ref_id', 'order_no', 'payer_name', 'to_bank_acc_no', 'to_bank_name', 'alipay', 'created_at', 'updated_at'
    ];

    public function customer()
    {
        return $this->hasOne('App\User','id','user_id');
    }
}
