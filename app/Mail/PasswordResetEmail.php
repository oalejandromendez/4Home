<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The resetPassword object instance.
     *
     * @var fullname
     */
    public $fullname;
    public $password;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($fullname, $password)
    {
        $this->fullname = $fullname;
        $this->password = $password;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Restablecer contraseña')->view('resetPassword');
    }
}
