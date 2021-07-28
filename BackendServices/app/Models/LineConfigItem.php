<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class LineConfigItem
 * 
 * @property string $id
 * @property string $agent_id
 * @property string $item_name
 * @property string $chanel
 * @property string $secret_key
 * @property string $token
 * @property string $webhook_link
 * @property string $back_office_url
 * @property bool $active_status
 * @property int $webhook_status
 * @property string $created_by
 * @property string $updated_by
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models
 */
class LineConfigItem extends Model
{
	protected $table = 'line_config_item';
	public $incrementing = false;

	protected $casts = [
		'active_status' => 'bool',
		'webhook_status' => 'int'
	];

	protected $hidden = [
		'secret_key',
		'token'
	];

	protected $fillable = [
		'agent_id',
		'item_name',
		'chanel',
		'secret_key',
		'token',
		'webhook_link',
		'back_office_url',
		'active_status',
		'webhook_status',
		'created_by',
		'updated_by'
	];
}
