<?php

namespace Tests\Feature\Admin\Membership;

use App\VitalGym\Entities\Customer;
use App\VitalGym\Entities\Membership;
use App\VitalGym\Entities\Payment;
use App\VitalGym\Entities\Plan;
use App\VitalGym\Entities\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EditMembershipTest extends TestCase
{
    use RefreshDatabase;

    private function validParams($overrides = [])
    {
        $customer = factory(Customer::class)->create();

        return array_merge([
            'date_start'          => now(),
            'date_end'            => now()->addMonth(1)->toDateString(),
            'total_days'            => 30,
            'customer_id'         => $customer->id,
            'membership_quantity' => 1,
        ], $overrides);
    }

    /** @test */
    function an_admin_can_view_the_edit_membership_form()
    {
        $this->withoutExceptionHandling();

        $adminUser = factory(User::class)->states('admin', 'active')->create();
        factory(Customer::class)->times(10)->create();
        $customer = factory(Customer::class)->create();
        $plan = factory(Plan::class)->create();
        $payment = factory(Payment::class)->create();
        $membership = factory(Membership::class)->create([
            'customer_id' => $customer->id,
            'plan_id' => $plan->id,
            'payment_id' => $payment->id,
        ]);

        $customers = Customer::all();
        $response = $this->actingAs($adminUser)->get(route('admin.memberships.edit', $membership));

        $response->assertSuccessful();
        $this->assertTrue($response->data('membership')->is($membership));
        $this->assertTrue($response->data('customer')->is($customer));
        $customers->assertEquals($response->data('customers'));
        $this->assertTrue($response->data('payment')->is($payment));
        $this->assertTrue($response->data('plan')->is($plan));
    }
    
    /** @test */
    function see_error_404_when_attempting_to_view_the_membership_edit_form_if_membership_does_not_exist()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->get(route('admin.memberships.edit', '999'));

        $response->assertStatus(404);
    }

    /** @test */
    function admin_can_edit_normal_membership()
    {
        $this->withoutExceptionHandling();
        $adminUser = factory(User::class)->states('admin', 'active')->create();
        $otherCustomer = factory(Customer::class)->create();
        $customer = factory(Customer::class)->create();
        $plan = factory(Plan::class)->create(['price' => 2000]);
        $payment = factory(Payment::class)->create([
            'total_price' => 2000,
            'membership_quantity' => 1,
            'customer_id' => $customer->id,
            'user_id' => $adminUser->id
        ]);
        $membership = factory(Membership::class)->create([
            'date_start' => now()->parse('15-07-2018'),
            'date_end' => now()->parse('16-07-2018'),
            'plan_id' => $plan->id,
            'customer_id' => $customer->id,
            'payment_id' => $payment->id,
        ]);
        $date_start = now();
        $date_end = now()->addMonth(1);

        $response = $this->be($adminUser)->patch(route('admin.memberships.update', $membership), $this->validParams([
            'date_start'          => $date_start,
            'date_end'            => $date_end,
            'total_days'          => '',
            'customer_id'         => $otherCustomer->id,
            'membership_quantity' => 2,
        ]));

        $response->assertRedirect(route('admin.memberships.index'));
        tap($membership->fresh(), function ( $membership ) use ($otherCustomer, $adminUser, $date_start, $date_end) {
            $this->assertEquals($date_start->toDateString(), $membership->date_start->toDateString());
            $this->assertEquals($date_end->toDateString(), $membership->date_end->toDateString());
            $this->assertEquals($otherCustomer->id, $membership->customer_id);
            $this->assertEquals(4000, $membership->payment->total_price);
            $this->assertEquals($otherCustomer->id, $membership->payment->customer_id);
            $this->assertEquals($adminUser->id, $membership->payment->user_id);
        });
        $response->assertSessionHas('alert-type', 'success');
    }

    /** @test */
    function admin_can_edit_a_premium_membership()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();
        $otherCustomer = factory(Customer::class)->create();
        $customer = factory(Customer::class)->create();
        $plan = factory(Plan::class)->states('premium')->create(['price' => 2000]);
        $payment = factory(Payment::class)->create([
            'total_price' => 2000,
            'membership_quantity' => 1,
            'customer_id' => $customer->id,
            'user_id' => $adminUser->id
        ]);
        $membership = factory(Membership::class)->create([
            'date_start' => now()->parse('15-07-2018'),
            'date_end' => now()->parse('16-07-2018'),
            'total_days' => 10,
            'plan_id' => $plan->id,
            'customer_id' => $customer->id,
            'payment_id' => $payment->id,
        ]);
        $date_start = now();
        $date_end = now()->addMonth(1);

        $response = $this->be($adminUser)->patch(route('admin.memberships.update', $membership), [
            'date_start'          => $date_start,
            'date_end'            => $date_end,
            'total_days'            => 30,
            'customer_id'         => $otherCustomer->id,
            'membership_quantity' => 2,
        ]);

        $response->assertRedirect(route('admin.memberships.index'));
        tap($membership->fresh(), function ( $membership ) use ($otherCustomer, $adminUser, $date_start, $date_end) {
            $this->assertEquals($date_start->toDateString(), $membership->date_start->toDateString());
            $this->assertEquals($date_end->toDateString(), $membership->date_end->toDateString());
            $this->assertEquals(30, $membership->total_days);
            $this->assertEquals($otherCustomer->id, $membership->customer_id);
            $this->assertEquals(4000, $membership->payment->total_price);
            $this->assertEquals($otherCustomer->id, $membership->payment->customer_id);
            $this->assertEquals($adminUser->id, $membership->payment->user_id);
        });
        $response->assertSessionHas('alert-type', 'success');
    }
    
    /** @test */
    function see_a_404_error_when_attempting_to_update_a_membership_that_does_not_exist()
    {
        //$this->withoutExceptionHandling();
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->patch(route('admin.memberships.update', '999'));

        $response->assertStatus(404);
    }
    
    /** @test */
    function date_start_is_required_to_update_any_membership()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();
        $membership = factory(Membership::class)->create();

        $response = $this->be($adminUser)->patch(route('admin.memberships.update', $membership), $this->validParams([
            'date_start' => '',
        ]));

        $response->status(302);
        $response->assertSessionHasErrors('date_start');
    }

    /** @test */
    function date_start_must_be_a_valid_date_to_update_any_membership()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();
        $membership = factory(Membership::class)->create();

        $response = $this->be($adminUser)->patch(route('admin.memberships.update', $membership), $this->validParams([
            'date_start' => 'invalid-date',
        ]));

        $response->status(302);
        $response->assertSessionHasErrors('date_start');
    }

    /** @test */
    function date_start_can_be_less_than_the_current_date_to_update_any_membership()
    {
        $dateStart = now()->subDays(5);
        $adminUser = factory(User::class)->states('admin', 'active')->create();
        $membership = factory(Membership::class)->create([
            'date_start' => $dateStart,
        ]);

        $dateStart = $dateStart->addDays(1);

        $response = $this->be($adminUser)->patch(route('admin.memberships.update', $membership), $this->validParams([
            'date_start' => $dateStart,
        ]));

        $response->assertRedirect(route('admin.memberships.index'));
        tap($membership->fresh(), function ( $membership ) use ( $dateStart ) {
            $this->assertEquals($dateStart->toDateString(), $membership->date_start->toDateString());
        });
        $response->assertSessionHas('alert-type', 'success');
    }

    /** @test */
    function date_start_can_be_equals_to_the_date_previously_saved_to_update_any_membership()
    {
        $dateStart = now()->subDays(5);
        $adminUser = factory(User::class)->states('admin', 'active')->create();
        $membership = factory(Membership::class)->create([
            'date_start' => $dateStart,
        ]);

        $response = $this->be($adminUser)->patch(route('admin.memberships.update', $membership), $this->validParams([
            'date_start' => $dateStart,
        ]));

        $response->assertRedirect(route('admin.memberships.index'));
        tap($membership->fresh(), function ( $membership ) use ( $dateStart ) {
            $this->assertEquals($dateStart->toDateString(), $membership->date_start->toDateString());
        });
        $response->assertSessionHas('alert-type', 'success');
    }

    /** @test */
    function date_start_must_be_a_greater_than_or_equal_to_the_date_previously_saved_to_update_any_membership()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();
        $membership = factory(Membership::class)->create(['date_start' => now()->subDays(1)]);


        $response = $this->be($adminUser)->patch(route('admin.memberships.update', $membership), $this->validParams([
            'date_start' => $membership->date_start->subDays(1),
        ]));

        $response->status(302);
        $response->assertSessionHasErrors('date_start');
    }

    /** @test */
    function date_end_is_required_to_update_any_membership()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();
        $membership = factory(Membership::class)->create();

        $response = $this->be($adminUser)->patch(route('admin.memberships.update', $membership), $this->validParams([
            'date_end' => '',
        ]));

        $response->status(302);
        $response->assertSessionHasErrors('date_end');
    }

    /** @test */
    function date_end_must_be_a_valid_date_to_update_any_membership()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();
        $membership = factory(Membership::class)->create();

        $response = $this->be($adminUser)->patch(route('admin.memberships.update', $membership), $this->validParams([
            'date_end' => 'invalid-date',
        ]));

        $response->status(302);
        $response->assertSessionHasErrors('date_end');
    }

    /** @test */
    function date_end_must_be_a_greater_or_equal_to_start_date_to_update_any_membership()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();
        $membership = factory(Membership::class)->create();

        $response = $this->be($adminUser)->patch(route('admin.memberships.update', $membership), $this->validParams([
            'date_start' => now(),
            'date_end' => now()->subDays(1),
        ]));

        $response->status(302);
        $response->assertSessionHasErrors('date_end');
    }

    /** @test */
    function total_days_is_optional_to_update_a_normal_membership()
    {
        $this->withoutExceptionHandling();

        $adminUser = factory(User::class)->states('admin', 'active')->create();
        $membership = factory(Membership::class)->create();

        $response = $this->be($adminUser)->patch(route('admin.memberships.update', $membership), $this->validParams([
            'total_days' => '',
        ]));

        $response->assertRedirect(route('admin.memberships.index'));
        $response->assertSessionHas('alert-type', 'success');
    }

    /** @test */
    function total_days_is_required_to_update_a_premium_membership()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();
        $plan = factory(Plan::class)->states('premium')->create();
        $membership = factory(Membership::class)->create(['plan_id' => $plan->id]);

        $response = $this->be($adminUser)->patch(route('admin.memberships.update', $membership), $this->validParams([
            'total_days' => '',
        ]));

        $response->assertStatus(302);
        $response->assertSessionHasErrors('total_days');
    }

    /** @test */
    function total_days_must_be_an_integer_to_update_a_premium_membership()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();
        $plan = factory(Plan::class)->states('premium')->create();
        $membership = factory(Membership::class)->create(['plan_id' => $plan->id]);

        $response = $this->be($adminUser)->patch(route('admin.memberships.update', $membership), $this->validParams([
            'total_days' => 'no-integer',
        ]));

        $response->assertStatus(302);
        $response->assertSessionHasErrors('total_days');
    }

    /** @test */
    function total_days_must_be_greater_than_zero_to_update_a_premium_membership()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();
        $plan = factory(Plan::class)->states('premium')->create();
        $membership = factory(Membership::class)->create(['plan_id' => $plan->id]);

        $response = $this->be($adminUser)->patch(route('admin.memberships.update', $membership), $this->validParams([
            'total_days' => 0,
        ]));

        $response->assertStatus(302);
        $response->assertSessionHasErrors('total_days');
    }

    /** @test */
    function customer_id_is_required_to_update_any_membership()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();
        $membership = factory(Membership::class)->create();

        $response = $this->be($adminUser)->patch(route('admin.memberships.update', $membership), $this->validParams([
            'customer_id' => '',
        ]));

        $response->assertStatus(302);
        $response->assertSessionHasErrors('customer_id');
    }

    /** @test */
    function customer_id_must_be_exist_on_customer_table_to_update_any_membership()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();
        $membership = factory(Membership::class)->create();

        $response = $this->be($adminUser)->patch(route('admin.memberships.update', $membership), $this->validParams([
            'customer_id' => '999',
        ]));

        $response->assertStatus(302);
        $response->assertSessionHasErrors('customer_id');
    }

    /** @test */
    function membership_quantity_is_required_to_update_any_membership()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();
        $membership = factory(Membership::class)->create();

        $response = $this->be($adminUser)->patch(route('admin.memberships.update', $membership), $this->validParams([
            'membership_quantity' => '',
        ]));

        $response->assertStatus(302);
        $response->assertSessionHasErrors('membership_quantity');
    }

    /** @test */
    function membership_quantity_must_be_integer_to_update_any_membership()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();
        $membership = factory(Membership::class)->create();

        $response = $this->be($adminUser)->patch(route('admin.memberships.update', $membership), $this->validParams([
            'membership_quantity' => 'invalid-customer-id',
        ]));

        $response->assertStatus(302);
        $response->assertSessionHasErrors('membership_quantity');
    }

    /** @test */
    function membership_quantity_must_be_at_least_one_to_update_any_membership()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();
        $membership = factory(Membership::class)->create();

        $response = $this->be($adminUser)->patch(route('admin.memberships.update', $membership), $this->validParams([
            'membership_quantity' => 0,
        ]));

        $response->assertStatus(302);
        $response->assertSessionHasErrors('membership_quantity');
    }
}
