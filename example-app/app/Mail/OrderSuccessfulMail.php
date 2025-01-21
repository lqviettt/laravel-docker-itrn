<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderSuccessfulMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    public function __construct($order)
    {
        $this->order = $order;
    }

    public function build()
    {
        return $this->from('quocviet30042003@gmail.com', 'QuocViet')
            ->subject('Đơn hàng của bạn đã được đặt thành công!')
            ->view('emails.order-success', [
                'order' => $this->order
            ]);
    }
}
