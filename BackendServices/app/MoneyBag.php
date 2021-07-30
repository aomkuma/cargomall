<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MoneyBag extends Model
{
    //
    protected $table = 'money_bag';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
	public $incrementing = false;

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s','modified' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s','modified' => 'datetime:Y-m-d H:i:s',
    ];
    
    protected $fillable = [
        'id', 'user_id', 'balance', 'slip_path', 'created_at', 'updated_at'
    ];
}
