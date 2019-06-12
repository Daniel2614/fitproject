<?php

use Illuminate\Support\Facades\DB;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ResetPasswordTest extends BrowserKitTestCase
{
    use RefreshDatabase;

    /** @test */
    function password_reset_email()
    {
        Notification::fake();

        $user = $this->createNewUser();

        $this->visit('/')
            ->seePageIs('/login')
            ->click('Olvidé mi contraseña')
            ->seePageIs('/password/reset')
            ->type($user->email, 'email')
            ->press('Restablecer Contraseña');

        Notification::assertSentTo($user, ResetPasswordNotification::class);
        $this->seeText('¡Te hemos enviado por correo el enlace para restablecer tu contraseña!');
    }

    /** @test */
    function password_reset_email_not_received()
    {
        $this->visit('/')
            ->seePageIs('/login')
            ->click('Olvidé mi contraseña')
            ->seePageIs('/password/reset')
            ->type('fake@email.com', 'email')
            ->press('Restablecer Contraseña');

        $this->seeText('No podemos encontrar ningún usuario con ese correo electrónico.');
    }

    /** @test */
    function user_can_reset_password()
    {
        $token = str_random(60);
        $user = $this->createNewUser();

        DB::table('password_resets')->insert(['email' => $user->email, 'token' => bcrypt($token)]);

        $this->visit("/password/reset/{$token}")
            ->seeText('Restablecer la contraseña')
            ->type('tavo198718@gmail.com', 'email')
            ->type('laravel', 'password')
            ->type('laravel', 'password_confirmation')
            ->press('Restablecer la contraseña');

        $this->seeIsAuthenticated()
            ->seeText('Bienvenido');
    }

    /** @test */
    function unregistered_user_cannot_reset_password()
    {
        $token = str_random(60);

        $this->visit('/password/reset/'.$token)
            ->seeText('Restablecer la contraseña')
            ->type('fake@email.com', 'email')
            ->type('laravel', 'password')
            ->type('laravel', 'password_confirmation')
            ->press('Restablecer la contraseña');

        $this->dontSeeIsAuthenticated()
            ->dontSeeCredentials(['password' => 'laravel'])
            ->seeText('No podemos encontrar ningún usuario con ese correo electrónico.');
    }

    /** @test */
    function invalid_token_to_password_reset()
    {
        $user = $this->createNewUser();
        $token = str_random(60);
        $fake_token = str_random(60);

        DB::table('password_resets')->insert(['email' => $user->email, 'token' => $token]);

        $this->visit('/password/reset/'.$fake_token)
            ->seeText('Restablecer la contraseña')
            ->type($user->email, 'email')
            ->type('laravel', 'password')
            ->type('laravel', 'password_confirmation')
            ->press('Restablecer la contraseña');

        $this->dontSeeIsAuthenticated()
            ->dontSeeCredentials(['password' => 'laravel'])
            ->seeText('El token de recuperación de contraseña es inválido.');
    }

    /** @test */
    function required_fields_to_password_reset()
    {
        $token = str_random(60);

        $this->visit('/password/reset/'.$token)
            ->press('Restablecer la contraseña');

        $this->seeText('El campo correo electrónico es obligatorio.')
            ->seeText('El campo contraseña es obligatorio.');
    }
}
