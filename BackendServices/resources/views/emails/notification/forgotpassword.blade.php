<h3>เรียนคุณ {{$email_data['firstname']}} {{$email_data['lastname']}}</h3>
<br>แจ้ง URL สำหรับการรีเซ็ตรหัสผ่านใหม่ สำหรับอีเมล {{ $email_data['email'] }} คือ 
<br>
{{ $email_data['url'] }}
<br>
<br>
Thanks,<br>
{{ config('app.name') }}