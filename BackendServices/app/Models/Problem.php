<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Problem
 * 
 * @property int $id
 * @property int $user_id
 * @property int $admin_id
 * @property string $user_comment
 * @property string $admin_coment
 * @property string $status
 * @property Carbon $close_datetime
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models
 */
class Problem extends Model
{
	protected $table = 'problems';

	protected $casts = [
	// 	'user_id' => 'int',
	// 	'admin_id' => 'int'
		'close_datetime' => 'datetime:Y-m-d H:i:s','modified' => 'datetime:Y-m-d H:i:s',
		'created_at' => 'datetime:Y-m-d H:i:s','modified' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s','modified' => 'datetime:Y-m-d H:i:s',
	];

	protected $fillable = [
		'user_id',
		'admin_id',
		'title',
		'user_comment',
		'admin_comment',
		'status',
		'close_datetime'
	];

	public function user()
    {
        return $this->hasOne('App\User','id','user_id');
    }

    public function admin()
    {
        return $this->hasOne('App\UserAdmin','id','admin_id');
    }
}
