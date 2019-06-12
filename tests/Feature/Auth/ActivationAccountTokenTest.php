<?php

use App\Mail\SendActivationToken;
use Illuminate\Support\Facades\Mail;
use App\Listeners\SendActivationEmail;
use App\VitalGym\Entities\ActivationToken;
use App\Events\UserRequestedActivationEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ActivationAccountTokenTest extends BrowserKitTestCase
{
    use RefreshDatabase;

    /** @test */
    function user_can_resend_email_verification_with_token()
    {
        $this->expectsEvents(UserRequestedActivationEmail::class);

        $user = $this->createNewUser(['active' => false]);
        factory(ActivationToken::class, 1)->create([
            'user_id' => $user->id,
        ]);

        $this->visit('/login')
            ->type($user->email, 'email')
            ->type('secret', 'password')
            ->press('Entrar')
            ->seePageIs('/login')
            ->dontSeeIsAuthenticated()
            ->seeText('Por favor verifica tu email para activar tu cuenta')
            ->click('Reenviar email de verificación');

        $this->seeText('Email de verificación enviado');
    }

    /** @test */
    function user_cannot_resend_email_verification_with_token()
    {
        $listener = Mockery::spy(SendActivationEmail::class);
        app()->instance(SendActivationEmail::class, $listener);

        $user = $this->createNewUser(['active' => false]);
        factory(ActivationToken::class)->create([
            'user_id' => $user->id,
        ]);

        $this->visit('/login')
            ->type($user->email, 'email')
            ->type('secret', 'password')
            ->press('Entrar')
            ->seePageIs('/login')
            ->dontSeeIsAuthenticated()
            ->seeText('Por favor verifica tu email para activar tu cuenta')
            ->click('Reenviar email de verificación');

        $listener->shouldHaveReceived('handle')->with(Mockery::on(function ($event) use ($user) {
            return $event->user->id == $user->id;
        }))->once();

        $this->seeText('Email de verificación enviado');
    }

    /** @test */
    function user_can_receive_email_with_activation_token()
    {
        Mail::fake();
        $user = $this->createNewUser(['active' => false]);

        $token = factory(ActivationToken::class)->create([
            'user_id' => $user->id,
        ]);

        $this->get(route('auth.activate.resend', $user->email));

        Mail::assertSent(SendActivationToken::class, function ($mail) use ($token, $user) {
            return $mail->hasTo($user->email) && $mail->token->token === $token->token;
        });
    }

    /** @test */
    function unregistered_user_cannot_get_email_with_activation_token()
    {
        $this->get(route('auth.activate.resend', 'fake@email.com'));

        $this->assertResponseStatus(404);
    }
}
