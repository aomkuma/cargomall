<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    //
    protected $table = 'user_address';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
	public $incrementing = false;
    
    protected $fillable = [
        'id', 'user_id', 'address1', 'address2', 'address3', 'address4', 'address5', 'address6', 'address7', 'address_no', 'created_at', 'updated_at'
    ];
}
