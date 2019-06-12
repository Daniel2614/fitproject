<?php

namespace Tests\Unit\Schedule;

use Tests\TestCase;
use Illuminate\Console\Scheduling\Event;
use Illuminate\Console\Scheduling\Schedule;

class MembershipPaymentReminderScheduleTest extends TestCase
{
    /** @test */
    public function membership_payment_reminder_must_be_run_daily_and_available_in_the_scheduler()
    {
        $schedule = app()->make(Schedule::class);

        $events = collect($schedule->events())->filter(function (Event $event) {
            return stripos($event->command, 'notify:membership-payment-reminder');
        });

        if ($events->count() == 0) {
            $this->fail('Command notify:membership-payment-reminder was not registered in Kernel.php');
        }

        $events->each(function (Event $event) {
            $this->assertContains('notify:membership-payment-reminder', $event->command);
            $this->assertEquals('0 9 * * *', $event->getExpression());
        });
    }
}
