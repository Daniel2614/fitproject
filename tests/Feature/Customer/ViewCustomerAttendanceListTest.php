<?php

namespace Tests\Feature\Customer;

use App\VitalGym\Entities\Attendance;
use App\VitalGym\Entities\Customer;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ViewCustomerAttendanceListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function a_customer_can_view_his_attendances()
    {
        $this->withoutExceptionHandling();
        factory(Attendance::class)->times(2)->create();
        $john = factory(Customer::class)->states('active')->create();
        factory(Attendance::class)->times(5)->create(['customer_id' => $john->id]);


        $response = $this->be($john->user)->get(route('customer.attendances.index'));

        $response->assertSuccessFul();
        $response->assertViewIs('customer.attendances.index');
        $this->assertEquals(5, $response->data('attendances')->count());
    }
}
