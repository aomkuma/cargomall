<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TopupNotiMailable extends Mailable
{
    use Queueable, SerializesModels;
    protected $topup;
    protected $money_bag;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($topup, $money_bag)
    {
        //
        $this->topup = $topup;
        $this->money_bag = $money_bag;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.notification.topup')
                ->subject("แจ้งผลการเติมเงิน (" . ( $this->topup->topup_status == 2?'อนุมัติการเติมเงิน':'ปฏิเสธการเติมเงิน' ) . ")")
                ->with(['topup' => $this->topup, 'money_bag' => $this->money_bag]);
    }
}
