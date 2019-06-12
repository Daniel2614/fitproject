<?php

namespace Tests\Feature\Admin\Attendance;

use App\VitalGym\Entities\Attendance;
use App\VitalGym\Entities\Customer;
use App\VitalGym\Entities\Membership;
use App\VitalGym\Entities\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AddAttendanceTest extends TestCase
{
    use RefreshDatabase;

    private $customer;
    private $date;

    private function  validParams($overrides = [])
    {
        $this->customer = factory(Customer::class)->states('active')->create();
        factory(Membership::class)->create([
            'customer_id' => $this->customer->id,
            'date_end' => now()->addMonth()->format('Y-m-d H:i:s'),
        ]);

        return array_merge([
            'date' => now()->format('Y-m-d H:i:s'),
            'customer_id' => $this->customer->id
        ], $overrides);
    }

   /** @test */
   function an_admin_can_view_the_form_to_create_a_new_attendance()
   {
       $this->withoutExceptionHandling();
       $adminUser = factory(User::class)->states('admin', 'active')->create();
       factory(Customer::class)->times(4)->create();

       $response = $this->be($adminUser)->get(route('admin.attendances.create'));

       $response->assertSuccessFul();
       $expectedCustomers = Customer::orderByDesc('id')->paginate();
       $response->assertViewIs('admin.attendances.create');
       $response->assertViewHas('customers', $expectedCustomers);
   }

   /** @test */
   function an_admin_can_create_an_attendance()
   {
       $this->withoutExceptionHandling();
       $adminUser = factory(User::class)->states('admin', 'active')->create();

       $response = $this->be($adminUser)->post(route('admin.attendances.store'), $this->validParams());

       $response->assertRedirect(route('admin.attendances.index'));
       tap(Attendance::first(), function ($attendance) {
           $this->assertEquals($this->customer->id, $attendance->customer_id);
       });

       $response->assertSessionHas('alert-type', 'success');
       $response->assertSessionHas('message');
   }

    /** @test */
    function an_admin_can_create_an_attendance_although_the_active_membership_expires_today()
    {
        $this->withoutExceptionHandling();
        $adminUser = factory(User::class)->states('admin', 'active')->create();
        $customer = factory(Customer::class)->state('active')->create();
        factory(Membership::class)->create(['customer_id' => $customer->id, 'date_end' => now()]);

        $response = $this->be($adminUser)->post(route('admin.attendances.store'), $this->validParams(['customer_id' => $customer->id]));

        $response->assertRedirect(route('admin.attendances.index'));
        tap(Attendance::first(), function ($attendance) use ($customer) {
            $this->assertEquals($customer->id, $attendance->customer_id);
        });

        $response->assertSessionHas('alert-type', 'success');
        $response->assertSessionHas('message');
    }

      /** @test */
   function date_is_required()
   {
       $adminUser = factory(User::class)->states('admin', 'active')->create();

       $response = $this->be($adminUser)->from(route('admin.attendances.create'))->post(route('admin.attendances.store'), $this->validParams([
           'date' => '',
       ]));

        $response->assertRedirect(route('admin.attendances.create'));
        $response->assertSessionHasErrors('date');
        $this->assertEquals(0, Attendance::count());
   }

    /** @test */
    function date_must_be_a_valid_date()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->from(route('admin.attendances.create'))->post(route('admin.attendances.store'), $this->validParams([
            'date' => 'invalid-date',
        ]));

        $response->assertRedirect(route('admin.attendances.create'));
        $response->assertSessionHasErrors('date');
        $this->assertEquals(0, Attendance::count());
    }

    /** @test */
    function date_must_have_a_valid_format()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->from(route('admin.attendances.create'))->post(route('admin.attendances.store'), $this->validParams([
            'date' => '2018-12-31',
        ]));

        $response->assertRedirect(route('admin.attendances.create'));
        $response->assertSessionHasErrors('date');
        $this->assertEquals(0, Attendance::count());
    }

    /** @test */
    function date_must_be_less_than_today()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->from(route('admin.attendances.create'))->post(route('admin.attendances.store'), $this->validParams([
            'date' => now()->addMinutes(30)->format('Y-m-d H:i:s'),
        ]));

        $response->assertRedirect(route('admin.attendances.create'));
        $response->assertSessionHasErrors('date');
        $this->assertEquals(0, Attendance::count());
    }

    /** @test */
    function customer_id_is_required()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->from(route('admin.attendances.create'))->post(route('admin.attendances.store'), $this->validParams([
            'customer_id' => '',
        ]));

        $response->assertRedirect(route('admin.attendances.create'));
        $response->assertSessionHasErrors('customer_id');
        $this->assertEquals(0, Attendance::count());
    }

    /** @test */
    function customer_id_must_be_exist()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->from(route('admin.attendances.create'))->post(route('admin.attendances.store'), $this->validParams([
            'customer_id' => '9999',
        ]));

        $response->assertRedirect(route('admin.attendances.create'));
        $response->assertSessionHasErrors('customer_id');
        $this->assertEquals(0, Attendance::count());
    }

    /** @test */
    function a_customer_can_only_have_one_attendance_per_day()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();
        $customer = factory(Customer::class)->states('active')->create();
        factory(Attendance::class)->create(['date' => today()->toDateString(), 'customer_id' => $customer->id]);
        factory(Membership::class)->create(['customer_id' => $customer->id]);

        $response = $this->be($adminUser)
                         ->from(route('admin.attendances.create'))
                         ->post(route('admin.attendances.store', $this->validParams([
                             'customer_id' => $customer->id
                         ])));

        $response->assertRedirect(route('admin.attendances.create'));
        $response->assertSessionHasErrors('customer_id');
        $this->assertEquals(1, Attendance::count());
    }

    /** @test */
    function a_customer_must_have_at_least_one_existing_membership()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();
        $customer = factory(Customer::class)->create();

        $response = $this->be($adminUser)
                         ->from(route('admin.attendances.create'))
                         ->post(route('admin.attendances.store', $this->validParams(['customer_id' => $customer->id])));

        $response->assertRedirect(route('admin.attendances.create'));
        $response->assertSessionHasErrors('customer_id');
        $this->assertEquals(0, Attendance::count());
    }

    /** @test */
    function a_customer_must_have_an_active_membership_to_create_an_attendance()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();
        $customer = factory(Customer::class)->create();
        factory(Membership::class)->create([
            'customer_id' => $customer->id,
            'date_end' => now()->subDay(1)
        ]);

        $response = $this->be($adminUser)
                         ->from(route('admin.attendances.create'))
                         ->post(route('admin.attendances.store', $this->validParams(['customer_id' => $customer->id])));

        $response->assertRedirect(route('admin.attendances.create'));
        $response->assertSessionHasErrors('customer_id');
        $this->assertEquals(0, Attendance::count());
    }
}
