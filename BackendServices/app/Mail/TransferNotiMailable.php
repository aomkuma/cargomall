<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TransferNotiMailable extends Mailable
{
    use Queueable, SerializesModels;
    protected $transfer;
    protected $money_bag;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($transfer, $money_bag)
    {
        //
        $this->transfer = $transfer;
        $this->money_bag = $money_bag;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.notification.transfer')
                ->subject("แจ้งผลการโอนเงิน (" . ( $this->transfer->pay_status == 2?'อนุมัติการโอนเงิน':'ปฏิเสธการโอนเงิน' ) . ")")
                ->with(['transfer' => $this->transfer, 'money_bag' => $this->money_bag]);
    }
}
