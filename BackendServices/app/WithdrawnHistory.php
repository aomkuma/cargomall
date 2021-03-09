<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WithdrawnHistory extends Model
{
    //
    protected $table = 'withdrawn_history';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
	public $incrementing = false;
    
    protected $fillable = [
        'id', 'user_id', 'withdrawn_amount', 'withdrawn_by', 'remark', 'created_at', 'updated_at'
    ];
}
