<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CustomerServiceSchedulingEmail extends Mailable
{
    use Queueable, SerializesModels;

     /**
     * The resetPassword object instance.
     *
     * @var fullname
     */
    public $fullname;
    public $reference;
    public $value;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($fullname, $reference, $value)
    {
        $this->fullname = $fullname;
        $this->reference = $reference;
        $this->value = $value;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Agendamiento de servicio')->view('customerServiceScheduling');
    }
}
