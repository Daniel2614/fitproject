<?php

namespace Tests\Feature\Admin\Membership;

use App\VitalGym\Entities\Membership;
use App\VitalGym\Entities\Payment;
use App\VitalGym\Entities\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeleteMembershipTest extends TestCase
{
    use RefreshDatabase;

   /** @test */
   function an_admin_can_delete_a_membership()
   {
       $adminUser = factory(User::class)->states('admin', 'active')->create();
       $membership = factory(Membership::class)->create();

       $response = $this->be($adminUser)->delete(route('admin.memberships.destroy', $membership));

       $response->assertRedirect(route('admin.memberships.index'));
       $this->assertEquals(0, Membership::count());
       $this->assertEquals(0, Payment::count());
       $response->assertSessionHas('alert-type', 'success');
   }
}
