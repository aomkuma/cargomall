แจ้งผลการเติมเงิน

<br>
<h3 style="color : #C91313;">สถานะ : {{ $topup->topup_status == 2?'อนุมัติการเติมเงิน':'ปฏิเสธการเติมเงิน' }}</h3>
<br>

@if($topup->topup_status == 2)
ยอดเงินเข้ากระเป๋าของคุณ : {{ $topup->customer->firstname }} {{ $topup->customer->lastname }}
<br>
จำนวนเงินที่เติม : {{$topup->topup_amount}} บาท
<br><br>
@endif

ยอดเงินคงเหลือในกระเป๋าของคุณคือ {{ $money_bag->balance }} บาท
<br>
Thanks,<br>
{{ config('app.name') }}