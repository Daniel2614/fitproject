<?php

namespace Tests\Unit\Schedule;

use Illuminate\Console\Scheduling\Event;
use Illuminate\Console\Scheduling\Schedule;
use Tests\TestCase;

class ScheduleExpiredMembershipTest extends TestCase
{
    /** @test */
    public function it_must_be_run_daily_and_available_in_the_scheduler()
    {
        $schedule = app()->make(Schedule::class);

        $events = collect($schedule->events())->filter(function (Event $event) {
            return stripos($event->command, 'notify:expired-membership');
        });

        if ($events->count() == 0) {
            $this->fail('Command notify:expired-membership was not registered in Kernel.php');
        }

        $events->each(function (Event $event) {
            $this->assertContains('notify:expired-membership', $event->command);
            $this->assertEquals('0 7 * * *', $event->getExpression());
        });
    }
}
