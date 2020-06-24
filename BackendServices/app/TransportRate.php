<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransportRate extends Model
{
    //
    protected $table = 'transport_rate';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
	public $incrementing = false;
    
    protected $fillable = [
        'id','rate_level', 'transport_type', 'rate_by_condition', 'product_desc', 'rate_1', 'rate_2', 'rate_3', 'created_at', 'updated_at'
    ];
}
