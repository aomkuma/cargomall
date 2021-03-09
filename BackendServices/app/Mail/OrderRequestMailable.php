<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderRequestMailable extends Mailable
{
    use Queueable, SerializesModels;
    protected $order;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($order)
    {
        //
        $this->order = $order;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.order.request')
                ->subject("รายละเอียดคำสั่งซื้อ (" . $this->order->order_no . ")")
                ->with([
                'order' => $this->order]);
    }
}
