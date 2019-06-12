<?php

namespace Tests\Unit\Entities;

use Illuminate\Database\Eloquent\Collection;
use Tests\TestCase;
use App\VitalGym\Entities\User;
use App\VitalGym\Entities\Customer;
use App\VitalGym\Entities\Membership;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CustomerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function a_customer_has_memberships()
    {
        $customer = factory(Customer::class)->make();

        $this->assertInstanceOf(
            'Illuminate\Database\Eloquent\Collection', $customer->memberships
        );
    }

    /** @test */
    function a_customer_has_a_user()
    {
        $customer = factory(Customer::class)->make();

        $this->assertInstanceOf(
            User::class, $customer->user
        );
    }

    /** @test */
    function a_customer_load_user_relationship_by_default()
    {
        $user = $this->createNewUser();
        $customer = factory(Customer::class)->create(['user_id' => $user->id]);

        $customer = $customer->fresh();

        $this->assertContains($user->fresh()->toArray(), $customer->toArray());
    }

    /** @test */
    function a_customer_has_many_attendances()
    {
        $customer = factory(Customer::class)->make();

        $this->assertInstanceOf(Collection::class, $customer->attendances);
    }

    /** @test */
    function it_return_only_customers_with_membership_expired_today()
    {
        $customerWithOldMembership = factory(Customer::class)->create();
        $customerWithExpiredMembershipToday = factory(Customer::class)->create();
        $otherCustomerWithExpiredMembershipToday = factory(Customer::class)->create();
        $customerWithActiveMembership = factory(Customer::class)->create();

        factory(Membership::class)->create(['date_end' => now()->subDays(1), 'customer_id' => $customerWithOldMembership->id]);
        factory(Membership::class)->create(['date_end' => now()->today(), 'customer_id' => $customerWithExpiredMembershipToday->id]);
        factory(Membership::class)->create(['date_end' => now()->today(), 'customer_id' => $customerWithExpiredMembershipToday->id]);
        factory(Membership::class)->create(['date_end' => now()->today(), 'customer_id' => $otherCustomerWithExpiredMembershipToday->id]);
        factory(Membership::class)->create(['date_end' => now()->addDay(), 'customer_id' => $customerWithActiveMembership->id]);

        $customersWithExpiredMembership = Customer::membershipExpiredToday();

        $this->assertFalse($customersWithExpiredMembership->contains($customerWithOldMembership));
        $this->assertTrue($customersWithExpiredMembership->contains($customerWithExpiredMembershipToday));
        $this->assertTrue($customersWithExpiredMembership->contains($customerWithExpiredMembershipToday));
        $this->assertFalse($customersWithExpiredMembership->contains($customerWithActiveMembership));
        $this->assertEquals(2, $customersWithExpiredMembership->count());
    }
}
