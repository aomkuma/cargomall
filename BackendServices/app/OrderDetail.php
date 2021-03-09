<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    //
    protected $table = 'order_detail';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
	public $incrementing = false;

	protected $casts = [
		'product_promotion_price' => 'float',
		'product_price_thb' => 'float',
		'product_promotion_price_thb' => 'float',
		'product_price_yuan' => 'float',
		'product_choose_amount' => 'int'
	];
    
    protected $fillable = [
        'id', 'order_id', 'product_original_url', 'product_thumbail_path', 'product_name', 'product_price_yuan', 'product_price_thb', 'product_promotion_price', 'product_promotion_price_thb', 'product_choose_color_img', 'product_reserve_color_img', 'product_choose_color', 'product_reserve_color', 'product_choose_size', 'product_reserve_size', 'product_choose_amount', 'remark', 'product_cancelled', 'created_at', 'updated_at'
    ];
}
