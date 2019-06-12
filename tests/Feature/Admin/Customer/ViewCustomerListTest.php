<?php

namespace Tests\Feature\Admin\Customer;

use App\VitalGym\Entities\Customer;
use App\VitalGym\Entities\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ViewCustomerListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function an_admin_can_view_the_list_of_customers()
    {
        $this->withoutExceptionHandling();
        $adminUser = factory(User::class)->states('admin', 'active')->create();
        factory(Customer::class)->times(5)->create();

        $response = $this->be($adminUser)->get(route('admin.customers.index'));

        $response->assertSuccessful();
        $expectedCustomers = Customer::with('user', 'level')->orderByDesc('id')->paginate();
        $response->assertViewIs('admin.customers.index');
        $response->assertViewHas('customers', $expectedCustomers);
    }

    /** @test */
    function an_admin_can_filter_a_customer_by_ci()
    {
        $this->withoutExceptionHandling();
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $customer = factory(Customer::class)->create(['ci' => '0926687856']);
        factory(Customer::class)->times(3)->create();

        $response = $this->be($adminUser)->get(route('admin.customers.index', [
            'ci' => $customer->ci
        ]));

        $response->assertSuccessful();
        $filteredCustomers = Customer::with('user', 'level')->where('ci', 'like', '%0926687856')->orderByDesc('id')->paginate();

        $response->assertViewIs('admin.customers.index');
        $response->assertViewHas('customers', $filteredCustomers);
        $this->assertEquals(1, $response->data('customers')->count());
    }

    /** @test */
    function an_admin_can_filter_a_customer_by_email()
    {
        $this->withoutExceptionHandling();
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $user = factory(User::class)->states('customer')->create(['email' => 'john@example.com']);
        $customer = factory(Customer::class)->create(['user_id' => $user->id]);
        factory(Customer::class)->times(3)->create();

        $response = $this->be($adminUser)->get(route('admin.customers.index', [
            'email' => $customer->user->email
        ]));

        $response->assertSuccessful();

        $response->assertViewIs('admin.customers.index');
        $this->assertEquals(1, $response->data('customers')->count());
    }
}
