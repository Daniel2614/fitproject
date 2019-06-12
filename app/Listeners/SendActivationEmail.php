<?php

namespace App\Listeners;

use App\Mail\SendActivationToken;
use Illuminate\Support\Facades\Mail;
use App\Events\UserRequestedActivationEmail;

class SendActivationEmail
{
    /**
     * Handle the event.
     *
     * @param  UserRequestedActivationEmail  $event
     * @return void
     */
    public function handle(UserRequestedActivationEmail $event)
    {
        Mail::to($event->user)->send(new SendActivationToken($event->user->token));
    }
}
