<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WithdrawnHistory extends Model
{
    //
    protected $table = 'withdrawn_history';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id', 'user_id', 'withdrawn_amount', 'withdrawn_by', 'created_at', 'updated_at'
    ];
}
