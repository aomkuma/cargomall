<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MoneyTopup extends Model
{
    //
    protected $table = 'money_topup';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;
    
    protected $fillable = [
        'id', 'user_id', 'from_account', 'to_account', 'topup_amount', 'topup_date', 'slip_file', 'topup_status', 'created_at', 'updated_at'
    ];

    public function customer()
    {
        return $this->hasOne('App\User','id','user_id');
    }
}
