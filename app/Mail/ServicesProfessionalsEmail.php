<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ServicesProfessionalsEmail extends Mailable
{
    use Queueable, SerializesModels;

     /**
     * The resetPassword object instance.
     *
     *
     */
    public $fullname;
    public $reference;
    public $client;
    public $address;
    public $workingDay;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($fullname, $reference, $client, $address, $workingDay)
    {
        $this->fullname = $fullname;
        $this->reference = $reference;
        $this->client = $client;
        $this->address = $address;
        $this->workingDay = $workingDay;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Agenda diaria')->view('servicesProfessionals');
    }
}
