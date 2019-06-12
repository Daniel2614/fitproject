<?php

namespace Tests\Unit\Notifications;

use App\Notifications\ExpiredMembership;
use App\VitalGym\Entities\Customer;
use App\VitalGym\Entities\Membership;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExpiredMembershipTest extends TestCase
{
  use RefreshDatabase;

  /** @test */
  function notification_via_email_must_be_contain_a_subject()
  {
      $customer = factory(Customer::class)->create();
      $membership = factory(Membership::class)->state('expired')->create(['customer_id' => $customer->id]);

      $notification = new ExpiredMembership($membership);

      $emailData = $notification->toMail($customer);

      $this->assertEquals($emailData->subject, 'Membresía Expirada');
  }

    /** @test */
    function notification_via_email_must_be_contain_a_greeting_with_customer_full_name()
    {
        $customer = factory(Customer::class)->create();
        $membership = factory(Membership::class)->state('expired')->create(['customer_id' => $customer->id]);

        $notification = new ExpiredMembership($membership);

        $emailData = $notification->toMail($customer);

        $this->assertEquals($emailData->greeting, "Hola {$customer->full_name}");
    }

    /** @test */
    function notification_via_email_must_be_contain_the_expired_membership_data()
    {
        $customer = factory(Customer::class)->create();
        $membership = factory(Membership::class)->state('expired')->create(['customer_id' => $customer->id]);
        $notification = new ExpiredMembership($membership);

        $emailData = $notification->toMail($customer);

        $this->assertContains("Tu membresía {$membership->plan->name} ha caducado", $emailData->introLines[0]);
        $this->assertContains("Fecha de inicio: {$membership->date_start->toDateString()}", $emailData->introLines[1]);
        $this->assertContains("Fecha de vencimiento: {$membership->date_end->toDateString()}", $emailData->introLines[2]);
    }
}
