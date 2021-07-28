<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class LineAutoReplyMessage
 * 
 * @property string $id
 * @property string $agent_id
 * @property string $type
 * @property string $name
 * @property string $announce
 * @property string $active_status
 * @property string $created_by
 * @property string $updated_by
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models
 */
class LineAutoReplyMessage extends Model
{
	protected $table = 'line_auto_reply_message';
	public $incrementing = false;

	protected $fillable = [
		'agent_id',
		'type',
		'name',
		'announce',
		'active_status',
		'created_by',
		'updated_by'
	];
}
