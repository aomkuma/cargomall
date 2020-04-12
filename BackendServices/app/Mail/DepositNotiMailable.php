<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DepositNotiMailable extends Mailable
{
    use Queueable, SerializesModels;
    protected $deposit;
    protected $money_bag;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($deposit, $money_bag)
    {
        //
        $this->deposit = $deposit;
        $this->money_bag = $money_bag;
    
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.notification.deposit')
                ->subject("แจ้งผลการเติมเงิน (" . ( $this->deposit->pay_status == 2?'ยืนยันการฝากจ่าย (สำเร็จ)':'ยกเลิกการฝากจ่าย' ) . ")")
                ->with(['deposit' => $this->deposit, 'money_bag' => $this->money_bag]);
    }
}
