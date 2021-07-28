<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PaymentHistory
 * 
 * @property int $id
 * @property int $user_id
 * @property int $reference_id
 * @property int $payment_type
 * @property string $url
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models
 */
class PaymentHistory extends Model
{
	protected $table = 'payment_history';

	protected $casts = [
		'user_id' => 'int',
		'reference_id' => 'int',
		'payment_type' => 'int'
	];

	protected $fillable = [
		'user_id',
		'reference_id',
		'payment_type',
		'url'
	];

	public function moneyUse()
    {
        return $this->hasOne('App\MoneyUse','id','reference_id');
    }

    public function moneyTopup()
    {
        return $this->hasOne('App\MoneyTopup','id','reference_id');
    }
    
}
