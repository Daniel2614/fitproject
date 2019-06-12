<?php

namespace Tests\Feature\Admin\Customer;

use App\VitalGym\Entities\Customer;
use App\VitalGym\Entities\User;
use Illuminate\Support\Facades\Facade;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ViewCustomerDetailsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function an_admin_can_view_the_customer_details()
    {
        $this->withoutExceptionHandling();
        $adminUser = factory(User::class)->states('admin', 'active')->create();
        $customer = factory(Customer::class)->create();

        $response = $this->be($adminUser)->get(route('admin.customers.show', $customer));

        $response->assertSuccessFul();
        $response->assertViewIs('admin.customers.show');
        $this->assertTrue($response->data('customer')->is($customer));
    }

    /** @test */
    function an_admin_cannot_view_a_customer_if_does_not_exist()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->get(route('admin.customers.show', '99999'));

        $response->assertStatus(404);
    }
}
