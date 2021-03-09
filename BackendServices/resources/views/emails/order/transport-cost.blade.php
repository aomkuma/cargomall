แจ้งการชำระค่าขนส่ง

<br>
<h3 style="color : #C91313;">สถานะ : ชำระเงินค่าขนส่งแล้ว</h3>
<br>
หมายเลข order : {{ $order->order_no }}
<br>
ผู้ชำระ {{ $order->customer->firstname }} {{ $order->customer->lastname }}
<br>
วันที่ชำระ {{ $pay->created_at }}
<br>
จำนวนเงินที่ชำระ : {{$pay->pay_amount_thb}} บาท
<br><br>
ยอดเงินคงเหลือในกระเป๋าของคุณคือ {{ $money_bag->balance }} บาท

@component('mail::button', ['url' => 'https://cargomall.co.th/tracking/order' ])
ท่านสามารถตรวจสอบสถานะสินค้าได้ที่นี่
@endcomponent

Thanks,<br>
{{ config('app.name') }}