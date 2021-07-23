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
    public $profesionaName;
    public $profesionaId;
    public $address;


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($fullname, $reference, $value, $profesionaName, $profesionaId, $address)
    {
        $this->fullname = $fullname;
        $this->reference = $reference;
        $this->value = $value;
        $this->profesionaName = $profesionaName;
        $this->profesionaId = $profesionaId;
        $this->address = $address;
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
