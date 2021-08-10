<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

use DateTimeInterface;

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
		'payment_type' => 'int',
        'created_at' => 'datetime:Y-m-d H:i:s','modified' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s','modified' => 'datetime:Y-m-d H:i:s',
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

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
    
}
