<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ImporterCostMailable extends Mailable
{
    use Queueable, SerializesModels;
    protected $importer;
    protected $pay;
    protected $money_bag;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($importer, $pay, $money_bag)
    {
        //
        $this->importer = $importer;
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
        return $this->markdown('emails.importer.cost')
                ->subject("ยืนยันการชำระค่าบริการนำเข้าสินค้า (" . $this->importer->tracking_no . ")")
                ->with(['importer' => $this->importer, 'pay' => $this->pay, 'money_bag' => $this->money_bag]);
    }
}
