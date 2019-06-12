<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthenticationUserTest extends BrowserKitTestCase
{
    use RefreshDatabase;

    /** @test */
    function user_can_login()
    {
        $user = $this->createNewUser();

        $this->visit('/')
            ->seePageIs('/login')
            ->type($user->email, 'email')
            ->type('secret', 'password')
            ->press(trans('login.buttonsign'));

        $this->SeeIsAuthenticated();
    }

    /** @test */
    function guest_user_cannot_login()
    {
        $this->visit('/')
            ->seePageIs('/login')
            ->press('Entrar');

        $this->seePageIs('/login')
            ->seeText('El campo correo electrónico es obligatorio')
            ->seeText('El campo contraseña es obligatorio')
            ->dontSeeIsAuthenticated();
    }

    /** @test */
    function user_can_logout()
    {
        $user = $this->createNewUser();

        $this->actingAs($user)
            ->visit('/')
            ->see('Bienvenido')
            ->seeText($user->name)
            ->post('/logout');

        $this->dontSeeIsAuthenticated()
            ->visit('/')
            ->seePageIs('login');
    }
}
