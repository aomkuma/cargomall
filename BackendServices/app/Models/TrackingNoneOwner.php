<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

use DateTimeInterface;

/**
 * Class TrackingNoneOwner
 * 
 * @property int $id
 * @property int $tracking_id
 * @property string $description
 * @property string $image_path
 * @property int $track_status
 * @property Carbon $receive_date
 * @property string $received_by
 * @property int $admin_approve_by
 * @property int $created_by
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models
 */
class TrackingNoneOwner extends Model
{
	protected $table = 'tracking_none_owner';

	protected $casts = [
		// 'tracking_id' => 'int',
		'track_status' => 'int',
		'admin_approve_by' => 'int',
		'created_by' => 'int',
        'created_at' => 'datetime:Y-m-d H:i:s','modified' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s','modified' => 'datetime:Y-m-d H:i:s',
	];

	protected $dates = [
		'receive_date'
	];

	protected $fillable = [
		'tracking_id',
		'description',
		'image_path',
		'track_status',
		'receive_date',
		'received_by',
		'admin_approve_by',
		'created_by'
	];

	public function orderTracking()
    {
        return $this->hasOne('App\OrderTracking','id','tracking_id');
    }

    public function customer()
    {
        return $this->hasOne('App\User','id','received_by');
    }

    public function admin()
    {
        return $this->hasOne('App\UserAdmin','id','admin_approve_by');
    }

    public function orderTrackingNotOwner()
    {
        return $this->hasOne('App\Models\OrderTrackingNotOwner','id','tracking_id');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
    
}
