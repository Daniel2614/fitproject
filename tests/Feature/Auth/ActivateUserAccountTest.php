<?php

use App\VitalGym\Entities\ActivationToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActionAccountTest extends TestCase
{
    use  RefreshDatabase;

    /** @test */
    function inactive_user_cannot_login()
    {
        $user = $this->createNewUser([
            'active' => false,
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'secret',
        ]);

        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    /** @test  */
    function inactive_user_cannot_login_even_if_reset_his_password()
    {
        $user = $this->createNewUser([
            'active' => false,
        ]);

        $token = str_random(60);
        DB::table('password_resets')->insert(['email' => $user->email, 'token' => $token]);

        $response = $this->post('/password/reset/', [
            'email' => $user->email,
            'password' => 'secret',
            'password_confirmation' => 'secret',
        ]);

        $response->assertStatus(302);
        $this->assertGuest();
    }

    /** @test */
    function user_can_activate_account()
    {
        $user = $this->createNewUser(['active' => false]);

        $token = factory(ActivationToken::class)->create([
            'user_id' => $user->id,
        ]);

        $response = $this->get(route('auth.activate.account', ['token' => $token]));

        $response->assertRedirect('/');
        $this->assertAuthenticated();

        $this->assertDatabaseMissing('activation_tokens', [
            'id'      => $token->id,
            'token'   => $token->token,
            'user_id' => $user->id,
        ]);

        $this->assertDatabaseHas('users', [
            'id'     => $user->id,
            'active' => true,
        ]);
    }

    /** @test  */
    function user_cannot_activate_account()
    {
        $user = $this->createNewUser(['active' => false]);
        $token = str_random(128);

        $response = $this->get(route('auth.activate.account', $token));

        $response->assertStatus(404);
        $this->assertGuest();
        $this->assertDatabaseHas('users', [
            'id'     => $user->id,
            'active' => false,
        ]);
    }
}
