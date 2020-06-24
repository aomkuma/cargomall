
รายละเอียดการสั่งซื้อ ( Order No. {{ $order->order_no }})
<br>
<h3 style="color : #C91313;">สถานะ : ชำระเงินค่าสินค้าแล้ว</h3>
<table width="100%">
	<tr>
		<td width="50%" style="vertical-align: top;">
			<b>รหัสลูกค้า : </b>{{$order->customer->user_code}}<br>
			<b>ชื่อลูกค้า : </b>{{$order->customer->firstname}} {{$order->customer->lastname}}<br>
			<b>อีเมล : </b>{{$order->customer->email}}<br>
			<b>เบอร์ติดต่อ : </b>{{$order->customer->mobile_no}}<br>
		</td>
		<td  width="50%" style="vertical-align: top; ">
			<b>ประเภทการขนส่ง : </b>{{$order->transport_type == 'sea'?'ทางเรือ':'ทางรถ'}}<br>
			<b>บริษัทขนส่ง : </b>{{$order->transport_company}}<br>
			<b>วิธีการรับสินค้า : </b>{{$order->receive_order_type == 'own'?'รับด้วยตนเอง':'จัดส่งตามที่อยู่'}}<br>
			@if($order->customerAddress)
			<b>ที่อยู่ในการจัดส่งสินค้า : </b><br>
			{{$order->customerAddress->address1}} 
			{{$order->customerAddress->address2}} 
			{{$order->customerAddress->address3}}<br>
			{{$order->customerAddress->address4}} 
			{{$order->customerAddress->address5}} 
			{{$order->customerAddress->address6}}<br>
			{{$order->customerAddress->address7}}
			@endif
			<br>
			<b>รายละเอียดเพิ่มเติม : </b>{{$order->addon}}
		</td>
	</tr>
</table>
<hr>
<table class="table table-bordered table-striped">
	<caption class="table-caption-head" style="font-size: 1.3em;">รายการสินค้า</caption>
	<tr class="default">
		<th style="vertical-align: top; text-align: center; width: 5%;">ลำดับ</th>
		<th style="vertical-align: top; text-align: center; width: 8%;">รูป</th>
		<th style="vertical-align: top; text-align: center;">ชื่อสินค้า</th>
		<th style="vertical-align: top; text-align: center; width: 16%;">สี  / สำรอง</th>
		<th style="vertical-align: top; text-align: center; width: 16%;">ขนาด / สำรอง </th>
		<th style="vertical-align: top; text-align: center; width: 10%;">ราคา (บาท)</th>
		<th style="vertical-align: top; text-align: center; width: 8%;">จำนวน</th>
		<th style="vertical-align: top; text-align: center; width: 9%;">รวม</th>
		<th style="vertical-align: top; text-align: center; width: 15%;">หมายเหตุ</th>
	</tr>

	<?php 
		$ex_rate = floatval($order->orderDesc->china_ex_rate) * 1;
		$sumBaht = 0; 
		$index = 1; 
	?>
	@foreach($order->orderDetails as $k => $product)
	<tr style="font-size: 0.9em;">
		<td style="vertical-align: top; ">{{$index}}</td>
		<td style="vertical-align: top; "><img src="{{$product->product_thumbail_path}}" style="max-width: 100%;" /></td>
		<td style="vertical-align: top; ">
			<a href="{{$product->product_original_url}}" target="_blank">{{ $product->product_name }}</a>
			<br>
			<span style="color: #FC9032; font-weight: bold;" >
				¥{{$product->product_price_yuan}}
			</span>
			@if($product->product_promotion_price != 0)
			<span style="color: green;">
				<br>
				ราคาโปรโมชั่น ¥{{$product->product_promotion_price}}
			</span>
			@endif
		</td>
		@if(!empty($product->product_choose_color_img))
		<td style="vertical-align: top; " >
			<div class="row">
				<div class="col-lg-12">
					<img src="{{$product->product_choose_color_img}}" style="max-width: 30px;" class="obj-align-child-center" /><br>
					สีสำรอง : {{$product->product_reserve_color_img}}
				</div>
			</div>
		</td>
		@endif

		@if( empty($product->product_choose_color_img))
		<td style="vertical-align: top; ">
			<div class="row">
				<div class="col-lg-12">
					<b>{{$product->product_choose_color}}</b><br>
					สีสำรอง : {{$product->product_reserve_color}}
				</div>
				
			</div>
		</td>
		@endif
		<td style="vertical-align: top; ">
			<div class="row">
				<div class="col-lg-12" style="word-break:normal; word-wrap:break-word;">
					<b>{{ is_array($product->product_choose_size) && count($product->product_choose_size) == 0?'': $product->product_choose_size}}</b><br>
					ขนาดสำรอง : {{$product->product_reserve_size}}
				</div>
			</div>
		</td>
		<td style="vertical-align: top; " align="right">
			<div class="row">
				<div class="col-lg-12" style="word-break:normal; word-wrap:break-word;">
					<span>{{ number_format($product->product_price_yuan *  $order->orderDesc->china_ex_rate , 2)}}</span>
				</div>
				@if($product->product_promotion_price > 0)
				<div class="col-lg-12" style="color: green;">
					{{ number_format($product->product_promotion_price * $order->orderDesc->china_ex_rate, 2) }}
				</div>
				@endif
			</div>
			
			
		</td>
		<td style="vertical-align: top; " align="right">
			{{$product->product_choose_amount}}
		</td>
		<td style="vertical-align: top; " align="right">{{ $product->product_promotion_price > 0 ? ($product->product_promotion_price * $order->orderDesc->china_ex_rate) * $product->product_choose_amount : ($product->product_price_yuan *  $order->orderDesc->china_ex_rate) * $product->product_choose_amount}}</td>
		<td style="vertical-align: top; " align="center">
			<div class="row">
				
				<div class="col-lg-12">
					{{$product->remark}}
				</div>
			</div>
		</td>
	</tr>
	<?php 

		if($product->product_promotion_price > 0){
            $sumBaht = $sumBaht + (($product->product_promotion_price * $ex_rate ) * $product->product_choose_amount);
        }else{
            $sumBaht = $sumBaht + (($product->product_price_yuan * $ex_rate ) * $product->product_choose_amount);
            
        }
		$index++;
	?>
	@endforeach
	<tfoot>
		<tr>
			<td colspan="7" align="right"><b style="font-size: 1.1em;">รวม</b></td>
			<td align="right"><b style="font-size: 1.1em;">{{ number_format($sumBaht, 2) }}</b></td>
			<td>&nbsp;</td>
		</tr>
	</tfoot>
</table>

@component('mail::button', ['url' => 'https://cargomall.co.th/tracking/order' ])
ท่านสามารถตรวจสอบสถานะสินค้าได้ที่นี่
@endcomponent

Thanks,<br>
{{ config('app.name') }}
