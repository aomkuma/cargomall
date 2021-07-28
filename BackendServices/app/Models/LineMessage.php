<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class LineMessage
 * 
 * @property int $id
 * @property int $member_id
 * @property string $agent_id
 * @property string $admin_id
 * @property string $line_user_id
 * @property string $message_type
 * @property string $message_group
 * @property string $message_desc
 * @property string $message_image
 * @property string $message_sticker
 * @property int $line_job_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string $line_config_item_id
 *
 * @package App\Models
 */
class LineMessage extends Model
{
	protected $table = 'line_message';

	protected $casts = [
		'member_id' => 'int',
		'line_job_id' => 'int'
	];

	protected $fillable = [
		'member_id',
		'agent_id',
		'admin_id',
		'line_user_id',
		'message_type',
		'message_group',
		'message_desc',
		'message_image',
		'message_sticker',
		'line_job_id',
		'line_config_item_id'
	];

	public function user()
	{
		return $this->hasOne('App\User','line_user_id','line_user_id');
	}
}
