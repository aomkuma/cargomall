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
    
    protected $fillable = [
        'id', 'user_id', 'balance', 'slip_path', 'created_at', 'updated_at'
    ];
}
