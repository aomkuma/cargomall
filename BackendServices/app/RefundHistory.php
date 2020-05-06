<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RefundHistory extends Model
{
    //
    protected $table = 'refund_history';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
	public $incrementing = false;
    
    protected $fillable = [
        'id', 'user_id', 'refund_amount', 'refund_by', 'remark', 'created_at', 'updated_at'
    ];
}
