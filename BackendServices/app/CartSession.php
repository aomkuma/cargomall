<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CartSession extends Model
{
    //
    protected $table = 'cart_session';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
	public $incrementing = false;
    protected $fillable = [
        'id', 'user_id', 'cart_desc', 'creaated_by', 'updated_by', 'created_at', 'updated_at'
    ];
}
