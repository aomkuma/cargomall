<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use DateTimeInterface;

class MoneyTopup extends Model
{
    //
    protected $table = 'money_topup';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s','modified' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s','modified' => 'datetime:Y-m-d H:i:s',
    ];
    
    protected $fillable = [
        'id', 'user_id', 'from_account', 'to_account', 'topup_amount', 'topup_date', 'slip_file', 'topup_status', 'created_at', 'updated_at'
    ];

    public function customer()
    {
        return $this->hasOne('App\User','id','user_id');
    }

     protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
