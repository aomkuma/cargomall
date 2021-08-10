<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

use DateTimeInterface;

/**
 * Class OrderActivityLog
 * 
 * @property int $id
 * @property string $log_type
 * @property int $order_id
 * @property string $description
 * @property int $admin_id
 * @property string $admin_name
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models
 */
class OrderActivityLog extends Model
{
	protected $table = 'order_activity_log';

	protected $casts = [
		'order_id' => 'int',
		'admin_id' => 'int'
	];

	protected $fillable = [
		'log_type',
		'order_id',
		'description',
		'admin_id',
		'admin_name'
	];

	protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
