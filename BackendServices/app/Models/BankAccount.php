<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class BankAccount
 * 
 * @property int $id
 * @property string $account_name
 * @property string $account_no
 * @property string $account_type
 * @property bool $is_active
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models
 */
class BankAccount extends Model
{
	protected $table = 'bank_account';

	protected $casts = [
		'is_active' => 'bool'
	];

	protected $fillable = [
		'account_name',
		'account_no',
		'account_type',
		'bank_name',
		'is_active'
	];
}
