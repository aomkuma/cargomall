<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ReceiptRunnung
 * 
 * @property int $id
 * @property int $years
 * @property int $running_no
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models
 */
class ReceiptRunnung extends Model
{
	protected $table = 'receipt_runnung';
	public $incrementing = false;

	protected $casts = [
		'id' => 'int',
		'years' => 'int',
	];

	protected $fillable = [
		'years',
		'running_no',
		'data_type',
		'data_id'
	];
}
