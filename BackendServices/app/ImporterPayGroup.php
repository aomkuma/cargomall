<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ImporterPayGroup extends Model
{
    //
    protected $table = 'importer_pay_group';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
	public $incrementing = false;
    
    protected $fillable = [
        'id', 'importer_group_id', 'user_id', 'user_code', 'created_at', 'updated_at'
    ];
}
