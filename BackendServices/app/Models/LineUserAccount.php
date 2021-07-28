<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class LineUserAccount
 * 
 * @property string $id
 * @property string $agent_id
 * @property string $username
 * @property string $password
 * @property string $user_class
 * @property string $user_type
 * @property string $line_user_id
 * @property string $job_status
 * @property string $admin_job_id
 * @property bool $active_status
 * @property string $created_by
 * @property string $updated_by
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string $display_name
 * @property string $profile_url
 * @property string $chat_type
 * @property string $line_config_item_id
 * @property string $member_remark
 *
 * @package App\Models
 */
class LineUserAccount extends Model
{
	protected $table = 'line_user_account';
	public $incrementing = false;

	protected $casts = [
		'active_status' => 'bool'
	];


	protected $fillable = [
		'user_class',
		'user_type',
		'line_user_id',
		'job_status',
		'admin_job_id',
		'active_status',
		'created_by',
		'updated_by',
		'display_name',
		'profile_url',
		'chat_type',
		'line_config_item_id',
		'member_remark'
	];
}
