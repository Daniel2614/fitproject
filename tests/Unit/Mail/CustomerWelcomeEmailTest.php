<?php

namespace Tests\Unit\Mail;

use App\Mail\CustomerWelcomeEmail;
use App\VitalGym\Entities\ActivationToken;
use App\VitalGym\Entities\Customer;
use App\VitalGym\Entities\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CustomerWelcomeEmailTest extends TestCase
{
   use RefreshDatabase;

   /** @test */
   function email_must_be_contain_the_customer_full_name()
   {
       $user = factory(User::class)->states('customer')->create(['name' => 'John', 'last_name' => ' Doe']);
       factory(ActivationToken::class)->create(['token' => 'abc12345', 'user_id' => $user->id]);
       $customer = factory(Customer::class)->create(['user_id' => $user->id]);

       $mail = new CustomerWelcomeEmail($customer);

       $this->assertContains($customer->full_name, $mail->render());
   }

    /** @test */
    function email_must_be_contain_the_activation_token_link()
    {
        $user = factory(User::class)->states('customer')->create();
        $token = factory(ActivationToken::class)->create(['token' => 'abc12345', 'user_id' => $user->id]);
        $customer = factory(Customer::class)->create(['user_id' => $user->id]);

        $mail = new CustomerWelcomeEmail($customer);

        $this->assertContains(route('auth.activate.account', $token->token), $mail->render());
    }
}
