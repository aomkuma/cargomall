<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ForgotPassUrl extends Model
{
    //
    protected $table = 'forgot_pass_url';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id', 'user_id', 'email', 'url', 'data_key', 'active_status', 'created_by', 'updated_by', 'created_at', 'updated_at'
    ];
}
