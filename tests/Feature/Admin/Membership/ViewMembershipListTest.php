<?php

namespace Tests\Feature\Admin\Membership;

use App\VitalGym\Entities\Customer;
use App\VitalGym\Entities\Membership;
use App\VitalGym\Entities\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ViewMembershipListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function an_admin_can_view_the_list_of_memberships()
    {
        $this->withoutExceptionHandling();
        $adminUser = factory(User::class)->states('admin', 'active')->create();
        factory(Membership::class)->times(5)->create();

        $response = $this->be($adminUser)->get(route('admin.memberships.index'));

        $response->assertSuccessful();
        $expectedMemberships = Membership::with('customer', 'plan')->paginate();
        $response->assertViewIs('admin.memberships.index');
        $response->assertViewHas('memberships', $expectedMemberships);
    }
    
    /** @test */
    function an_admin_can_filter_memberships_by_customer_name()
    {
        $this->withoutExceptionHandling();
        $adminUser = factory(User::class)->states('admin', 'active')->create();
        $customerUser = factory(User::class)->states('customer')->create(['name' => 'John']);
        $john = factory(Customer::class)->create(['user_id' => $customerUser->id]);
        factory(Membership::class)->times(3)->create(['customer_id' => $john->id]);
        factory(Membership::class)->times(5)->create();

        $response = $this->be($adminUser)->get(route('admin.memberships.index', [
            'name' => $john->name
        ]));

        $response->assertSuccessful();
        $johnMemberships = Membership::with('customer', 'plan')->where('customer_id', $john->id)->paginate();
        $response->assertViewIs('admin.memberships.index');
        $response->assertViewHas('memberships', $johnMemberships);
        $this->assertEquals(3, $response->data('memberships')->count());
    }

    /** @test */
    function an_admin_can_filter_memberships_by_customer_email()
    {
        $this->withoutExceptionHandling();
        $adminUser = factory(User::class)->states('admin', 'active')->create();
        $customerUser = factory(User::class)->states('customer')->create(['email' => 'john@example.com']);
        $john = factory(Customer::class)->create(['user_id' => $customerUser->id]);
        factory(Membership::class)->times(2)->create(['customer_id' => $john->id]);
        factory(Membership::class)->times(4)->create();

        $response = $this->be($adminUser)->get(route('admin.memberships.index', ['email' => $john->email]));

        $response->assertSuccessful();
        $johnMemberships = Membership::with('customer', 'plan')->where('customer_id', $john->id)->paginate();
        $response->assertViewHas('memberships', $johnMemberships);
        $response->assertViewIs('admin.memberships.index');
        $this->assertEquals(2, $response->data('memberships')->count());
    }
}
