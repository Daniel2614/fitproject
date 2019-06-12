<?php

namespace Tests\Feature\Admin\Plan;

use App\VitalGym\Entities\Membership;
use App\VitalGym\Entities\Plan;
use App\VitalGym\Entities\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeletePlanTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function an_admin_can_delete_a_plan()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();
        $plan = factory(Plan::class)->create();

        $response = $this->be($adminUser)->delete(route('admin.plans.destroy', $plan));

        $response->assertRedirect(route('admin.plans.index'));
        $this->assertEquals(0, Plan::count());
        $response->assertSessionHas('alert-type', 'success');
        $response->assertSessionHas('message');
    }

    /** @test */
    function cannot_delete_a_plan_that_has_memberships()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();
        $plan = factory(Plan::class)->create();
        factory(Membership::class)->create(['plan_id' => $plan->id]);

        $response = $this->be($adminUser)->delete(route('admin.plans.destroy', $plan));

        $response->assertRedirect(route('admin.plans.index'));
        $this->assertEquals(1, Plan::count());
        $response->assertSessionHas('alert-type', 'error');
        $response->assertSessionHas('message');
    }

    /** @test */
    function see_404_error_if_plan_does_not_exist()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->delete(route('admin.plans.destroy', '999'));

        $response->assertStatus(404);
    }
}
