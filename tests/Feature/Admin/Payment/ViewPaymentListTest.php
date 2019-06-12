<?php

namespace Tests\Feature\Admin\Payment;

use App\VitalGym\Entities\Membership;
use App\VitalGym\Entities\Payment;
use App\VitalGym\Entities\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ViewPaymentListTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    function an_admin_can_view_the_payments_page()
    {
        $this->withoutExceptionHandling();
        $adminUser = factory(User::class)->states('admin', 'active')->create();
        //Creating memberships to generate payments automatically
        factory(Membership::class)->times(5)->create();

        $response = $this->be($adminUser)->get(route('admin.payments.index'));

        $response->assertSuccessful();
        $response->assertViewIs('admin.payments.index');
        $expectedPayments = Payment::with('customer', 'user', 'membership.plan')->orderByDesc('created_at')->paginate();

        $response->assertViewHas('payments', $expectedPayments);
    }
}
