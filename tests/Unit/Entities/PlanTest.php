<?php

namespace Tests\Unit\Entities;

use Illuminate\Database\Eloquent\Collection;
use Tests\TestCase;
use App\VitalGym\Entities\Plan;

class PlanTest extends TestCase
{
    /** @test */
    function can_get_price_in_dollars_with_two_decimals()
    {
        $membership = factory(Plan::class)->make([
           'price' => 461,
        ]);

        $this->assertSame('4.61', $membership->price_in_dollars);
    }

    /** @test */
    function a_plan_has_many_memberships()
    {
        $plan = factory(Plan::class)->make();

        $this->assertInstanceOf(Collection::class, $plan->memberships);
    }
}
