<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderSuccessMail extends Mailable
{
    public $order;
    public $billing;

    public function __construct($order, $billing = null)
    {
        $this->order = $order;
        $this->billing = $billing;
    }

    public function build()
    {
        return $this->subject('Your Ticket Order')
            ->view('emails.order-success')
            ->with([
                'order'   => $this->order,
                'billing' => $this->billing
            ]);
    }
}