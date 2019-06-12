<?php

namespace Tests\Feature\Admin\Customer;

use App\VitalGym\Entities\Customer;
use App\VitalGym\Entities\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeleteCustomerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function an_can_delete_a_customer()
    {
        $this->withoutExceptionHandling();
        $adminUser = factory(User::class)->states('admin', 'active')->create();
        $customer = factory(Customer::class)->create();

        $response = $this->be($adminUser)->delete(route('admin.customers.destroy', $customer));

        $response->assertRedirect(route('admin.customers.index'));
        $this->assertEquals(0, Customer::count());
        $response->assertSessionHas('alert-type', 'success');
        $response->assertSessionHas('message');
    }

    /** @test */
    function see_404_error_if_try_to_delete_customer_does_not_exist()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->delete(route('admin.customers.destroy', '999'));

        $response->assertStatus(404);
    }
}
