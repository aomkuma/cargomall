<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class OrderTrackingNotOwner
 * 
 * @property int $id
 * @property int $user_id
 * @property int $order_id
 * @property string $order_no
 * @property string $china_order_no
 * @property string $china_tracking_no
 * @property string $tracking_no
 * @property string $tracking_no_thai
 * @property float $import_fee
 * @property float $net_price
 * @property float $estimate_cost
 * @property float $actual_cost
 * @property float $customer_return_actual_cost
 * @property float $discount
 * @property string $transport_type
 * @property string $payment_type
 * @property string $product_type
 * @property string $receive_order_type
 * @property string $customer_address_id
 * @property string $transport_company
 * @property string $transport_company_other
 * @property string $add_on
 * @property int $check_use_cash_balance
 * @property float $use_cash_balance
 * @property int $order_status
 * @property string $china_arrival_date
 * @property string $cargo
 * @property string $package_amount
 * @property string $weight_kg
 * @property string $longs
 * @property string $widths
 * @property string $heights
 * @property string $cbm
 * @property string $china_departure_date
 * @property string $bill_no
 * @property float $transport_cost_china
 * @property float $transport_cost_thai
 * @property string $goods_desc_th
 * @property string $goods_desc_en
 * @property string $thai_arrival_date
 * @property string $container_no
 * @property bool $payment_status
 * @property bool $is_tracking_none_owner
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property bool $is_tracking_none_owners
 *
 * @package App\Models
 */
class OrderTrackingNotOwner extends Model
{
	protected $table = 'order_tracking_not_owner';
	public $incrementing = false;

	protected $casts = [
		'id' => 'int',
		'user_id' => 'int',
		'order_id' => 'int',
		'import_fee' => 'float',
		'net_price' => 'float',
		'estimate_cost' => 'float',
		'actual_cost' => 'float',
		'customer_return_actual_cost' => 'float',
		'discount' => 'float',
		'check_use_cash_balance' => 'int',
		'use_cash_balance' => 'float',
		'order_status' => 'int',
		'transport_cost_china' => 'float',
		'transport_cost_thai' => 'float',
		'payment_status' => 'bool',
		'is_tracking_none_owner' => 'bool',
		'is_tracking_none_owners' => 'bool',
		'created_at' => 'datetime:Y-m-d H:i:s','modified' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s','modified' => 'datetime:Y-m-d H:i:s',
	];

	protected $fillable = [
		'id',
		'user_id',
		'order_id',
		'order_no',
		'china_order_no',
		'china_tracking_no',
		'tracking_no',
		'tracking_no_thai',
		'import_fee',
		'net_price',
		'estimate_cost',
		'actual_cost',
		'customer_return_actual_cost',
		'discount',
		'transport_type',
		'payment_type',
		'product_type',
		'receive_order_type',
		'customer_address_id',
		'transport_company',
		'transport_company_other',
		'add_on',
		'check_use_cash_balance',
		'use_cash_balance',
		'order_status',
		'china_arrival_date',
		'cargo',
		'package_amount',
		'weight_kg',
		'longs',
		'widths',
		'heights',
		'cbm',
		'china_departure_date',
		'bill_no',
		'transport_cost_china',
		'transport_cost_thai',
		'goods_desc_th',
		'goods_desc_en',
		'thai_arrival_date',
		'container_no',
		'payment_status',
		'is_tracking_none_owner',
		'is_tracking_none_owners'
	];
}
