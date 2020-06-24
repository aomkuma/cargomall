<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserSession extends Model
{
    protected $table = 'user_session';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
	public $incrementing = false;
    
    protected $fillable = [
        'id', 'user_id', 'created_at'
    ];
}
