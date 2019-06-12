<?php

namespace Tests\Unit\Notifications;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\VitalGym\Entities\Customer;
use App\VitalGym\Entities\Membership;
use App\Notifications\MembershipPaymentReminder;

class MembershipPaymentReminderTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function membership_payment_reminder_notification_via_email_must_be_contain_a_subject()
    {
        $customer = factory(Customer::class)->create();
        $membership = factory(Membership::class)->create(['customer_id' => $customer->id, 'date_end' => today()->addDays(5)]);

        $notification = new MembershipPaymentReminder($membership);

        $emailData = $notification->toMail($customer);

        $this->assertEquals($emailData->subject, 'Recordatorio pago membresía');
    }

    /** @test */
    function membership_payment_reminder_notification_via_email_must_be_contain_a_greeting_with_customer_full_name()
    {
        $customer = factory(Customer::class)->create();
        $membership = factory(Membership::class)->state('expired')->create(['customer_id' => $customer->id]);

        $notification = new MembershipPaymentReminder($membership);

        $emailData = $notification->toMail($customer);

        $this->assertEquals($emailData->greeting, "Hola {$customer->full_name}");
    }

    /** @test */
    function membership_payment_reminder_notification_via_email_must_be_contain_the_membership_data()
    {
        $customer = factory(Customer::class)->create();
        $membership = factory(Membership::class)->state('expired')->create(['customer_id' => $customer->id]);
        $notification = new MembershipPaymentReminder($membership);

        $emailData = $notification->toMail($customer);

        $this->assertContains("Solo queríamos recordarte que tu membresía {$membership->plan->name} va a expirar muy pronto", $emailData->introLines);
        $this->assertContains("Fecha de inicio: {$membership->date_start->toDateString()}", $emailData->introLines);
        $this->assertContains("Fecha de vencimiento: {$membership->date_end->toDateString()}", $emailData->introLines);
    }
}
