<?php

namespace Tests\Unit\Console\Commands;

use App\VitalGym\Entities\Customer;
use App\VitalGym\Entities\Membership;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Notification;
use App\Notifications\ExpiredMembership;

class SendExpiredMembershipNotificationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_send_expired_membership_notifications()
    {
        Notification::fake();

        $john = factory(Customer::class)->create();
        $jane = factory(Customer::class)->create();
        $ted  = factory(Customer::class)->create();

        factory(Membership::class)->create(['customer_id' => $john->id, 'date_end' => now()->today()]);
        factory(Membership::class)->create(['customer_id' => $john->id, 'date_end' => now()->today()]);
        factory(Membership::class)->create(['customer_id' => $jane->id, 'date_end' => now()->today()]);
        factory(Membership::class)->create(['customer_id' => $ted->id, 'date_end' => now()->subDays(5)]);

        $this->artisan('notify:expired-membership')
             ->expectsOutput('Sending notifications')
            ->expectsOutput('The notification has been sent');

        Notification::assertSentTo($john, ExpiredMembership::class);
        Notification::assertSentToTimes($john, ExpiredMembership::class,1);
        Notification::assertSentTo($jane, ExpiredMembership::class);
        Notification::assertSentToTimes($jane, ExpiredMembership::class,1);
        Notification::assertNotSentTo($ted, ExpiredMembership::class);
    }

    /** @test */
    function it_not_send_notifications_if_does_not_exists_expired_memberships()
    {
        Notification::fake();

        $ted  = factory(Customer::class)->create();

        factory(Membership::class)->create(['customer_id' => $ted->id, 'date_end' => now()->subDays(1)]);

        $this->artisan('notify:expired-membership')
             ->expectsOutput('Sending notifications')
             ->expectsOutput('There are no customers to notify');

        Notification::assertNotSentTo($ted, ExpiredMembership::class);
    }
}
