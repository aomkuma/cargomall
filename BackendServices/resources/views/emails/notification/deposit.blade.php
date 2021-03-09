แจ้งผลการฝากจ่าย

<br>
<h3 style="color : #C91313;">สถานะ : {{ $deposit->pay_status == 2?'ยืนยันการฝากจ่าย (สำเร็จ)':'ยกเลิกการฝากจ่าย' }}</h3>
<br>

@if($deposit->pay_status == 2)
จ่ายไปยังเลขที่ order : {{ $deposit->order_no }}
<br>
จำนวนเงินที่ฝากจ่าย : {{$deposit->pay_amount_yuan}} หยวน
<br>
เรทฝากจ่าย (ต่อ 1 หยวน) : {{ $deposit->exchange_rate}} บาท
<br>
จำนวนเงินที่ถูกหักจากกระเป๋าเงินของคุณ : {{ $deposit->pay_amount_thb }} บาท
<br><br>
@endif

ยอดเงินคงเหลือในกระเป๋าของคุณคือ {{ $money_bag->balance }} บาท
<br>
Thanks,<br>
{{ config('app.name') }}