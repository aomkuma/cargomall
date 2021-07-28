<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class LineJobDetail
 * 
 * @property int $id
 * @property string $line_user_id
 * @property int $member_id
 * @property string $admin_id
 * @property string $job_status
 * @property Carbon $job_done_time
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string $line_config_item_id
 *
 * @package App\Models
 */
class LineJobDetail extends Model
{
	protected $table = 'line_job_detail';

	protected $casts = [
		'member_id' => 'int'
	];

	protected $dates = [
		'job_done_time'
	];

	protected $fillable = [
		'line_user_id',
		'member_id',
		'admin_id',
		'job_status',
		'job_done_time',
		'line_config_item_id'
	];
}
