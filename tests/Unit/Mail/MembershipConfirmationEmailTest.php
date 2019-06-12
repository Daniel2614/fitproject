<?php

namespace tests\Unit\Mail;

use App\VitalGym\Entities\Plan;
use App\VitalGym\Entities\Payment;
use Tests\TestCase;
use App\VitalGym\Entities\User;
use App\VitalGym\Entities\Customer;
use App\VitalGym\Entities\Membership;
use App\Mail\MembershipOrderConfirmationEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MembershipConfirmationEmailTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function email_contain_the_customer_name()
    {
        $user = factory(User::class)->create([
           'name' => 'John',
        ]);

        $customer = factory(Customer::class)->create([
            'user_id' => $user->id,
        ]);

        $membership = factory(Membership::class)->create([
            'customer_id' => $customer->id,
        ]);

        $email = new MembershipOrderConfirmationEmail($membership);
        $rendered = $email->render();

        $this->assertContains('John', $rendered);
    }

    /** @test */
    function email_contain_the_membership_type_name()
    {
        $plan = factory(Plan::class)->create([
           'name' => 'mensual',
        ]);

        $membership = factory(Membership::class)->create([
            'plan_id' => $plan->id,
        ]);

        $email = new MembershipOrderConfirmationEmail($membership);
        $rendered = $email->render();

        $this->assertContains($plan->name, $rendered);
    }

    /** @test */
    function email_contain_the_membership_type_unit_price()
    {
        $plan = factory(Plan::class)->create([
            'price' => 2000,
        ]);

        $membership = factory(Membership::class)->create([
            'plan_id' => $plan->id,
        ]);

        $email = new MembershipOrderConfirmationEmail($membership);
        $rendered = $email->render();

        $this->assertContains('20.00', $rendered);
    }

    /** @test */
    function email_must_be_contain_the_membership_start_date()
    {
        $membership = factory(Membership::class)->create([
            'date_start' => now()->toDateString(),
        ]);

        $email = new MembershipOrderConfirmationEmail($membership);
        $rendered = $email->render();

        $this->assertContains($membership->date_start->format('d-m-Y'), $rendered);
    }

    /** @test */
    function email_must_be_contain_the_membership_date_end()
    {
        $membership = factory(Membership::class)->create([
            'date_end' => now()->addDays(30)->toDateString(),
        ]);

        $email = new MembershipOrderConfirmationEmail($membership);
        $rendered = $email->render();

        $this->assertContains($membership->date_end->format('d-m-Y'), $rendered);
    }

    /** @test */
    function email_must_be_contain_the_total_price_and_membership_quantity()
    {
        $payment = factory(Payment::class)->create([
            'total_price' => 4000,
            'membership_quantity' => 2,
        ]);
        $membership = factory(Membership::class)->create(['payment_id' => $payment->id]);


        $email = new MembershipOrderConfirmationEmail($membership);
        $rendered = $email->render();

        $this->assertContains('40.00', $rendered);
        $this->assertContains('2', $rendered);
    }
}
