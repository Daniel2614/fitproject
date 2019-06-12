<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\VitalGym\Entities\ActivationToken;

class SendActivationToken extends Mailable
{
    use SerializesModels;

    public $token;

    /**
     * SendActivationToken constructor.
     * @param ActivationToken $token
     */
    public function __construct(ActivationToken $token)
    {
        $this->token = $token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->subject('Activar Cuenta.')->view('emails.auth.activation');
    }
}
