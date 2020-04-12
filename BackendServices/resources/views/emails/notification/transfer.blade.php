แจ้งผลการโอนเงิน

<br>
<h3 style="color : #C91313;">สถานะ : {{ $transfer->pay_status == 2?'อนุมัติการโอนเงิน (สำเร็จ)':'ปฏิเสธการโอนเงิน' }}</h3>
<br>

@if($transfer->pay_status == 2)
ยอดเงินถูกโอนเข้าบัญชีธนาคารจีนชื่อ : {{ $transfer->payer_name }}
<br>
จำนวนเงินที่โอน : {{$transfer->pay_amount_yuan}} หยวน
<br>
เรทโอนเงิน (ต่อ 1 หยวน) : {{ $transfer->exchange_rate}} บาท
<br>
จำนวนเงินที่ถูกหักจากกระเป๋าเงินของคุณ : {{ $transfer->pay_amount_thb }} บาท
<br><br>
@endif

ยอดเงินคงเหลือในกระเป๋าของคุณคือ {{ $money_bag->balance }} บาท
<br>
Thanks,<br>
{{ config('app.name') }}