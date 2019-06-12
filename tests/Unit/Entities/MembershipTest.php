<?php

namespace Tests\Unit\Entities;

use Tests\TestCase;
use App\VitalGym\Entities\Payment;
use App\VitalGym\Entities\Customer;
use App\VitalGym\Entities\Membership;
use App\VitalGym\Entities\Plan;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MembershipTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function a_membership_has_a_customer()
    {
        $membership = factory(Membership::class)->create();

        $this->assertInstanceOf(
            Customer::class, $membership->customer
        );
    }

    /** @test */
    function a_membership_has_a_membershipType()
    {
        $membership = factory(Membership::class)->create();

        $this->assertInstanceOf(
            Plan::class, $membership->plan
        );
    }

    /** @test */
    function a_membership_belongs_to_payment()
    {
        $payment = factory(Payment::class)->create();
        $membership = factory(Membership::class)->create(['payment_id' => $payment->id]);

        $this->assertInstanceOf(
            Payment::class, $membership->payment
        );

        $this->assertEquals($payment->id, $membership->payment->id);
    }

    /** @test */
    function it_return_the_only_expired_memberships()
    {
        $membershipA = factory(Membership::class)->create(['date_end' => now()->toDateString()]);
        $membershipB = factory(Membership::class)->create(['date_end' => now()->toDateString()]);
        $activeMembership = factory(Membership::class)->create(['date_end' => now()->addDays(1)->toDateString()]);

        $expiredMemberships = Membership::expired()->get();

        $this->assertTrue($expiredMemberships->contains($membershipA));
        $this->assertTrue($expiredMemberships->contains($membershipB));
        $this->assertFalse($expiredMemberships->contains($activeMembership));
        $this->assertEquals(2, $expiredMemberships->count());
    }
}
