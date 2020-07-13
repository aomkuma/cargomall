<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderTransportCostMailable extends Mailable
{
    use Queueable, SerializesModels;
    protected $order;
    protected $pay;
    protected $money_bag;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($order, $pay, $money_bag)
    {
        //
        $this->order = $order;
        $this->pay = $pay;
        $this->money_bag = $money_bag;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.order.transport-cost')
                ->subject("ยืนยันการชำระค่าขนส่ง (" . $this->order->order_no . "), Track no. : " . $this->pay->to_ref_id_2)
                ->with(['order' => $this->order, 'pay' => $this->pay, 'money_bag' => $this->money_bag]);
    }
}
