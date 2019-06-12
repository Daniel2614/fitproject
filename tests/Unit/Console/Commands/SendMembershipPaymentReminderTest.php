<?php

namespace Tests\Unit\Console\Commands;

use Mail;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Notification;
use App\VitalGym\Entities\Customer;
use App\VitalGym\Entities\Membership;
use App\Notifications\MembershipPaymentReminder;

class SendMembershipPaymentReminderTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_send_a_membership_payment_reminder_notifications()
    {
        Notification::fake();
        Mail::fake();

        $john = factory(Customer::class)->create();
        $jane = factory(Customer::class)->create();
        $ted  = factory(Customer::class)->create();

        factory(Membership::class)->create(['customer_id' => $john->id, 'date_end' => now()->addDays(5)]);
        factory(Membership::class)->create(['customer_id' => $john->id, 'date_end' => now()->addDays(5)]);
        factory(Membership::class)->create(['customer_id' => $jane->id, 'date_end' => now()->addDays(5)]);
        factory(Membership::class)->create(['customer_id' => $ted->id, 'date_end' => today()]);

        $this->artisan('notify:membership-payment-reminder')
             ->expectsOutput('The notification has been sent');

        Notification::assertSentTo($john, MembershipPaymentReminder::class);
        Notification::assertSentToTimes($john, MembershipPaymentReminder::class, 1);
        Notification::assertSentTo($jane, MembershipPaymentReminder::class);
        Notification::assertNotSentTo($ted, MembershipPaymentReminder::class);
    }

    /** @test */
    function it_not_send_notifications_if_does_not_exists_memberships()
    {
        Notification::fake();

        $john = factory(Customer::class)->create();
        $jane = factory(Customer::class)->create();

        factory(Membership::class)->create(['customer_id' => $john->id, 'date_end' => now()->subDays(1)]);
        factory(Membership::class)->create(['customer_id' => $john->id, 'date_end' => now()->addDays(4)]);

        $this->artisan('notify:membership-payment-reminder')
             ->expectsOutput('There are not customers to notify');

        Notification::assertNotSentTo($john, MembershipPaymentReminder::class);
        Notification::assertNotSentTo($jane, MembershipPaymentReminder::class);
    }
}
