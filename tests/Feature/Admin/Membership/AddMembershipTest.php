<?php

namespace Tests\Feature\Admin\Membership;

use App\VitalGym\Entities\Membership;
use App\VitalGym\Entities\Payment;
use App\VitalGym\Entities\User;
use Tests\TestCase;
use App\VitalGym\Entities\Customer;
use Illuminate\Support\Facades\Mail;
use App\VitalGym\Entities\Plan;
use App\Mail\MembershipOrderConfirmationEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AddMembershipTest extends TestCase
{
    use RefreshDatabase;

    private $adminUser;

    public function setUp()
    {
        parent::setUp();
        $this->adminUser = $this->createNewUser();
    }

    private function orderMembership($params = [], $plan)
    {
        return $this->actingAs($this->adminUser)->post(route('admin.memberships.store',$plan), $params);
    }

    /** @test */
    function admin_can_view_the_membership_form_to_add_a_new_membership()
    {
        $this->withoutExceptionHandling();
        $customers = factory(Customer::class)->times(5)->create();
        $plan = factory(Plan::class)->create();

        $response = $this->actingAs($this->adminUser)->get(route('admin.memberships.create', $plan));

        $response->assertSuccessful();
        $this->assertTrue($response->data('plan')->is($plan));
        $customers->assertEquals($response->data('customers'));
        $response->assertViewIs('admin.memberships.create');
    }

    /** @test */
    function admin_see_a_404_when_attempting_to_view_the_create_membership_form_for_a_membership_type_that_does_not_exist()
    {
        $response = $this->actingAs($this->adminUser)->get(route('admin.memberships.create', 10));

        $response->assertStatus(404);
    }

    /** @test */
    function admin_can_add_a_normal_membership_for_a_new_customer()
    {
        $this->withoutExceptionHandling();
        Mail::fake();

        $customerUser = $this->createNewUser(['role' => 'customer', 'email' => 'john@example.com']);
        $dateStart = now()->toDateString();
        $dateEnd = now()->addMonth(1)->toDateString();
        $plan = factory(Plan::class)->create(['name' =>'Mensual', 'price' => 3000]);
        $customer = factory(Customer::class)->create(['user_id' => $customerUser->id]);

        $response = $this->orderMembership([
            'date_start' => $dateStart,
            'date_end' => $dateEnd,
            'customer_id' => $customer->id,
            'membership_quantity' => 2,
        ], $plan);
        $membership =Membership::first()->fresh();
        $payment = $membership->payment;

        $response->assertRedirect(route('admin.memberships.index'));
        $this->assertEquals(1, $membership->count());
        $this->assertEquals(1, $payment->count());
        $this->assertEquals($dateStart, $membership->date_start->toDateString());
        $this->assertEquals($dateEnd, $membership->date_end->toDateString());
        $this->assertEquals(2, $payment->membership_quantity);
        $this->assertEquals($customer->id, $payment->customer_id);
        $this->assertEquals(6000, $payment->total_price);
        $this->assertEquals($this->adminUser->id, $payment->user_id);
        $response->assertSessionHas('message');
        $response->assertSessionHas('alert-type', 'success');

        Mail::assertQueued(MembershipOrderConfirmationEmail::class, function ($mail) use ($membership) {
            return $mail->hasTo('john@example.com')
                && $mail->membership->id === $membership->id;
        });
    }

    /** @test */
    function admin_can_add_a_premium_membership_for_a_new_customer()
    {
        $this->withoutExceptionHandling();
        Mail::fake();

        $customerUser = factory(User::class)->states('active')->create(['role' => 'customer', 'email' => 'john@example.com']);
        $dateStart = now()->toDateString();
        $dateEnd = now()->addMonth(1)->toDateString();
        $plan = factory(Plan::class)->states('premium')->create(['name' =>'Mensual', 'price' => 4000,]);
        $customer = factory(Customer::class)->create(['user_id' => $customerUser->id]);

        $response = $this->orderMembership([
            'date_start' => $dateStart,
            'date_end' => $dateEnd,
            'total_days' => 30,
            'customer_id' => $customer->id,
            'membership_quantity' => 2,
        ], $plan);

        $membership =Membership::first()->fresh();
        $payment = $membership->payment;

        $response->assertRedirect(route('admin.memberships.index'));
        $this->assertEquals(1, $membership->count());
        $this->assertEquals(1, $payment->count());
        $this->assertEquals($dateStart, $membership->date_start->toDateString());
        $this->assertEquals($dateEnd, $membership->date_end->toDateString());
        $this->assertEquals(2, $payment->membership_quantity);
        $this->assertEquals($customer->id, $payment->customer_id);
        $this->assertEquals(8000, $payment->total_price);
        $this->assertEquals($this->adminUser->id, $payment->user_id);
        $this->assertEquals(30, $membership->total_days);
        $response->assertSessionHas('message');
        $response->assertSessionHas('alert-type', 'success');

        Mail::assertQueued(MembershipOrderConfirmationEmail::class, function ($mail) use ($membership) {
            return $mail->hasTo('john@example.com')
                   && $mail->membership->id === $membership->id;
        });
    }

    /** @test */
    function see_a_404_error_when_attempting_to_create_a_membership_with_a_plan_that_does_not_exists()
    {
        $customerUser = factory(User::class)->states('active')->create(['role' => 'customer', 'email' => 'john@example.com']);
        $dateStart = now()->toDateString();
        $dateEnd = now()->addMonth(1)->toDateString();
        $customer = factory(Customer::class)->create(['user_id' => $customerUser->id]);

        $response = $this->orderMembership([
            'date_start' => $dateStart,
            'date_end' => $dateEnd,
            'total_days' => 30,
            'customer_id' => $customer->id,
            'membership_quantity' => 2,
        ], 0);

        $response->assertStatus(404);
        $this->assertEquals(0, Membership::count());
        $this->assertEquals(0, Payment::count());
    }

    /** @test */
    function date_start_is_required_to_create_any_membership()
    {
        $plan = factory(Plan::class)->create();
        $customer = factory(Customer::class)->create();

        $response = $this->orderMembership([
            'date_end' => now()->toDateString(),
            'customer_id' => $customer->id,
            'membership_quantity' => 2,
        ], $plan);

        $response->status(302);
        $response->assertSessionHasErrors('date_start');
        $this->assertEquals(0, Membership::count());
    }

    /** @test */
    function date_start_must_be_a_valid_date_to_create_any_membership()
    {
        $plan = factory(Plan::class)->create();
        $customer = factory(Customer::class)->create();

        $response = $this->orderMembership([
            'date_start' => 'invalid-start-date',
            'date_end' => now()->toDateString(),
            'customer_id' => $customer->id,
            'membership_quantity' => 2,
        ], $plan);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('date_start');
        $this->assertEquals(0, Membership::count());
    }

    /** @test */
    function date_start_must_be_greater_or_equal_than_the_current_date_to_create_any_membership()
    {
        $plan = factory(Plan::class)->create();
        $customer = factory(Customer::class)->create();

        $response = $this->orderMembership([
            'date_start' => '1998-06-05',
            'date_end' => now()->toDateString(),
            'customer_id' => $customer->id,
            'membership_quantity' => 2,
        ], $plan);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('date_start');
        $this->assertEquals(0, Membership::count());
    }

    /** @test */
    function date_start_must_have_the_following_format_yyyy_mm_dd_to_create_any_membership()
    {
        $plan = factory(Plan::class)->create();
        $customer = factory(Customer::class)->create();

        $response = $this->orderMembership([
            'date_start' => now()->format('d-m-Y'),
            'date_end' => now()->toDateString(),
            'customer_id' => $customer->id,
            'membership_quantity' => 2,
        ], $plan);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('date_start');
        $this->assertEquals(0, Membership::count());

    }

    /** @test */
    function date_end_is_required_to_create_any_membership()
    {
        $plan = factory(Plan::class)->create();
        $customer = factory(Customer::class)->create();

        $response = $this->orderMembership([
            'date_start' => now()->toDateString(),
            'customer_id' => $customer->id,
            'membership_quantity' => 2,
        ], $plan);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('date_end');
        $this->assertEquals(0, Membership::count());
    }

    /** @test */
    function date_end_must_be_a_valid_date_to_create_any_membership()
    {
        $plan = factory(Plan::class)->create();
        $customer = factory(Customer::class)->create();

        $response = $this->orderMembership([
            'date_start' => now()->toDateString(),
            'date_end' => 'invalid-end-date',
            'customer_id' => $customer->id,
            'membership_quantity' => 2,
        ], $plan);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('date_end');
        $this->assertEquals(0, Membership::count());
    }

    /** @test */
    function date_end_must_have_the_following_format_yyyy_mm_dd_to_create_any_membership()
    {
        $plan = factory(Plan::class)->create();
        $customer = factory(Customer::class)->create();

        $response = $this->orderMembership([
            'date_start' => now()->toDateString(),
            'date_end' => now()->format('d-m-Y'),
            'customer_id' => $customer->id,
            'membership_quantity' => 2,
        ], $plan);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('date_end');
        $this->assertEquals(0, Membership::count());
    }

    /** @test */
    function the_end_date_must_be_greater_or_equal_than_start_date_to_create_any_membership()
    {
        $plan = factory(Plan::class)->create();
        $customer = factory(Customer::class)->create();
        $date = now();

        $response = $this->orderMembership([
            'date_start' => $date->toDateString(),
            'date_end' => $date->subDays(10)->toDateString(),
            'total_days' => 30,
            'customer_id' => $customer->id,
            'membership_quantity' => 2,
        ], $plan);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('date_end');
        $this->assertEquals(0, Membership::count());
    }

    /** @test */
    function total_days_is_required_to_create_a_premium_membership()
    {
        $plan = factory(Plan::class)->states('premium')->create();
        $customer = factory(Customer::class)->create();
        $date = now();

        $response = $this->orderMembership([
            'date_start' => $date->toDateString(),
            'date_end' => $date->addDays(30)->toDateString(),
            'customer_id' => $customer->id,
            'membership_quantity' => 2,
        ], $plan);

        $response->assertStatus(302);
        $this->assertEquals(0, Membership::count());
        $response->assertSessionHasErrors('total_days');
    }

    /** @test */
    function the_total_days_must_be_integer_to_create_a_premium_membership()
    {
        $plan = factory(Plan::class)->states('premium')->create();
        $customer = factory(Customer::class)->create();
        $date = now();

        $response = $this->orderMembership([
            'date_start' => $date->toDateString(),
            'date_end' => $date->addDays(30)->toDateString(),
            'total_days' => 'invalid-number-days',
            'customer_id' => $customer->id,
            'membership_quantity' => 2,
        ], $plan);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('total_days');
        $this->assertEquals(0, Membership::count());
    }

    /** @test */
    function total_days_must_be_at_least_1_to_create_a_premium_membership()
    {
        $plan = factory(Plan::class)->states('premium')->create();
        $customer = factory(Customer::class)->create();
        $date = now();

        $response = $this->orderMembership([
            'date_start' => $date->toDateString(),
            'date_end' => $date->addDays(30)->toDateString(),
            'total_days' => 0,
            'customer_id' => $customer->id,
            'membership_quantity' => 2,
        ], $plan);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('total_days');
        $this->assertEquals(0, Membership::count());
    }

    /** @test */
    function the_customer_is_required_to_create_any_membership()
    {
        $plan = factory(Plan::class)->create();
        $date = now();

        $response = $this->orderMembership([
            'date_start' => $date->toDateString(),
            'date_end' => $date->addDays(30)->toDateString(),
            'membership_quantity' => 2,
        ], $plan);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('customer_id');
        $this->assertEquals(0, Membership::count());
    }

    /** @test */
    function customer_must_exist_to_create_any_membership()
    {
        $plan = factory(Plan::class)->create();
        $date = now();

        $response = $this->orderMembership([
            'date_start' => $date->toDateString(),
            'date_end' => $date->addDays(30)->toDateString(),
            'customer_id' => 1,
            'membership_quantity' => 2,
        ], $plan);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('customer_id');
        $this->assertEquals(0, Membership::count());
    }

    /** @test */
    function membership_quantity_is_required_to_create_any_membership()
    {
        $plan = factory(Plan::class)->create();
        $customer = factory(Customer::class)->create();
        $date = now();

        $response = $this->orderMembership([
            'date_start' => $date->toDateString(),
            'date_end' => $date->addDays(30)->toDateString(),
            'customer_id' => $customer->id,
        ], $plan);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('membership_quantity');
        $this->assertEquals(0, Membership::count());
    }

    /** @test */
    function membership_quantity_must_be_a_integer_to_create_any_membership()
    {
        $plan = factory(Plan::class)->create();
        $customer = factory(Customer::class)->create();
        $date = now();

        $response = $this->orderMembership([
            'date_start' => $date->toDateString(),
            'date_end' => $date->addDays(30)->toDateString(),
            'customer_id' => $customer->id,
            'membership_quantity' => 'invalid-membership-quantity',
        ], $plan);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('membership_quantity');
        $this->assertEquals(0, Membership::count());
    }

    /** @test */
    function membership_quantity_must_be_at_least_1_to_create_any_membership()
    {
        $plan = factory(Plan::class)->create();
        $customer = factory(Customer::class)->create();
        $date = now();

        $response = $this->orderMembership([
            'date_start' => $date->toDateString(),
            'date_end' => $date->addDays(30)->toDateString(),
            'customer_id' => $customer->id,
            'membership_quantity' => 0,
        ], $plan);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('membership_quantity');
        $this->assertEquals(0, Membership::count());
    }
}
