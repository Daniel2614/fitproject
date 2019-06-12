<?php

namespace App\Http\Controllers\Auth;

use App\VitalGym\Entities\User;
use App\Http\Controllers\Controller;
use App\VitalGym\Entities\ActivationToken;
use App\Events\UserRequestedActivationEmail;

class ActivationController extends Controller
{
    public function activate(ActivationToken $token)
    {
        $user = $token->activateUserAccount();
        if ($user) {
            auth()->login($user);
        }

        return redirect('/')->with(['message' => 'Gracias por activar tu cuenta', 'alert-type' => 'success']);
    }

    public function resend($email)
    {
        $user = User::where('email', $email)->firstOrFail();

        if ($user->active) {
            return redirect('/');
        }

        event(new UserRequestedActivationEmail($user));

        return redirect('/login')->withInfo('Email de verificación enviado.');
    }
}
