<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CargoAddress
 * 
 * @property int $id
 * @property string $title
 * @property string $address
 * @property bool $is_active
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models
 */
class CargoAddress extends Model
{
	protected $table = 'cargo_address';
	public $incrementing = false;

	protected $casts = [
		'id' => 'int',
		'is_active' => 'bool'
	];

	protected $fillable = [
		'title',
		'address',
		'is_active'
	];
}
