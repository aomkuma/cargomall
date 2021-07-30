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

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s','modified' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s','modified' => 'datetime:Y-m-d H:i:s',
    ];
    
    protected $fillable = [
        'id', 'user_id', 'withdrawn_amount', 'withdrawn_by', 'remark', 'created_at', 'updated_at'
    ];
}
