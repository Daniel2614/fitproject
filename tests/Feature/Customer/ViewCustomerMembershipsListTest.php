<?php

namespace Tests\Feature\Customer;

use App\VitalGym\Entities\Customer;
use App\VitalGym\Entities\Membership;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ViewCustomerMembershipsListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function a_customer_can_view_only_his_memberships()
    {
        $this->withoutExceptionHandling();
        $john = factory(Customer::class)->states('active')->create();
        factory(Membership::class)->times(5)->create(['customer_id' => $john->id]);
        factory(Membership::class)->times(2)->create();

        $response = $this->be($john->user)->get(route('customer.memberships.index'));

        $response->assertSuccessFul();
        $response->assertViewIs('customer.memberships.index');
        $this->assertEquals(5, $response->data('memberships')->count());
    }
}
