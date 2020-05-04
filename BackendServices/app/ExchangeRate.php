<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExchangeRate extends Model
{
    //
    protected $table = 'exchange_rate';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
	public $incrementing = false;
    
    protected $fillable = [
        'id', 'exchange_rate', 'created_at', 'updated_at'
    ];
}
