<?php

namespace Tests\Feature\Customer;

use App\VitalGym\Entities\Customer;
use App\VitalGym\Entities\Payment;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ViewCustomerPaymentsListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function a_customer_can_view_only_his_payments()
    {
        $this->withoutExceptionHandling();
        $john = factory(Customer::class)->states('active')->create();
        factory(Payment::class)->times(2)->create();
        factory(Payment::class)->times(5)->create(['customer_id' => $john->id]);

        $response = $this->be($john->user)->get(route('customer.payments.index'));

        $response->assertSuccessFul();
        $response->assertViewIs('customer.payments.index');
        $this->assertEquals(5, $response->data('payments')->count());
    }
}
