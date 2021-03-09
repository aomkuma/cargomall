<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CustomerRequestOwner
 * 
 * @property int $id
 * @property int $user_id
 * @property int $tracking_none_owner_id
 * @property int $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models
 */
class CustomerRequestOwner extends Model
{
	protected $table = 'customer_request_owner';

	protected $casts = [
		'user_id' => 'int',
		'tracking_none_owner_id' => 'int',
		'status' => 'int'
	];

	protected $fillable = [
		'user_id',
		'tracking_none_owner_id',
		'status'
	];

	public function customer()
    {
        return $this->hasOne('App\User','id','user_id');
    }
}
