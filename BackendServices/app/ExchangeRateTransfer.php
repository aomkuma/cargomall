<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExchangeRateTransfer extends Model
{
    //
    protected $table = 'exchange_rate_transfer';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id', 'exchange_rate', 'created_at', 'updated_at'
    ];
}
