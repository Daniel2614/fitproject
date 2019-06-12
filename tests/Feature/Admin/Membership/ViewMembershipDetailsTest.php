<?php

namespace Tests\Feature\Admin\Membership;

use App\VitalGym\Entities\Customer;
use App\VitalGym\Entities\Membership;
use App\VitalGym\Entities\Payment;
use App\VitalGym\Entities\Plan;
use App\VitalGym\Entities\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ViewMembershipDetailsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function an_admin_can_view_membership_details()
    {
        $this->withoutExceptionHandling();

        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $customer = factory(Customer::class)->create();
        $plan = factory(Plan::class)->create();
        $payment = factory(Payment::class)->create();
        $membership = factory(Membership::class)->create([
            'customer_id' => $customer->id,
            'plan_id' => $plan->id,
            'payment_id' => $payment->id,
        ]);

        $response = $this->actingAs($adminUser)->get(route('admin.memberships.show', $membership));

        $response->assertSuccessful();
        $this->assertTrue($response->data('membership')->is($membership));
        $this->assertTrue($response->data('customer')->is($customer));
        $this->assertTrue($response->data('plan')->is($plan));
        $this->assertTrue($response->data('payment')->is($payment));
    }

    /** @test */
    function see_404_error_if_membership_does_not_exist()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->actingAs($adminUser)->get(route('admin.memberships.show', 0));

        $response->assertStatus(404);
    }
}
