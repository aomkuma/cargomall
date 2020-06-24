แจ้งการชำระค่าบริการนำเข้าสินค้า

<br>
<h3 style="color : #C91313;">สถานะ : ชำระเงินค่าขนส่งแล้ว</h3>
<br>
หมายเลข tracking : {{ $importer->tracking_no }}
<br>
ผู้ชำระ {{ $importer->customer->firstname }} {{ $importer->customer->lastname }}
<br>
วันที่ชำระ {{ $pay->created_at }}
<br>
จำนวนเงินที่ชำระ : {{$pay->pay_amount_thb}} บาท
<br><br>
ยอดเงินคงเหลือในกระเป๋าของคุณคือ {{ $money_bag->balance }} บาท

@component('mail::button', ['url' => 'https://cargomall.co.th/importer' ])
ท่านสามารถตรวจสอบสถานะของบริการนำเข้าสินค้าได้ที่นี่
@endcomponent

Thanks,<br>
{{ config('app.name') }}