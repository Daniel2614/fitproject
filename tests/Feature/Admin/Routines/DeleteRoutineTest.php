<?php

namespace Tests\Feature\tests\Feature\Admin\Routines;

use App\VitalGym\Entities\Customer;
use App\VitalGym\Entities\Routine;
use App\VitalGym\Entities\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeleteRoutineTest extends TestCase
{
    use RefreshDatabase;

    /**  @test */
    function it_can_delete_a_routine()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();
        $routine = factory(Routine::class)->create();

        $response = $this->be($adminUser)->from(route('admin.routines.index'))->delete(route('admin.routines.destroy', $routine));

        $response->assertRedirect(route('admin.routines.index'));
        $this->assertEquals(0, Routine::count());
        $response->assertSessionHas('alert-type', 'success');
        $response->assertSessionHas('message');
    }

    /**  @test */
    function cannot_delete_a_routine_if_has_customers()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();
        $routine = factory(Routine::class)->create();
        factory(Customer::class)->create(['routine_id' => $routine->id]);

        $response = $this->be($adminUser)->from(route('admin.routines.index'))->delete(route('admin.routines.destroy', $routine));

        $response->assertRedirect(route('admin.routines.index'));
        $this->assertEquals(1, Routine::count());
        $this->assertEquals(1, Customer::count());
        $response->assertSessionHas('alert-type', 'error');
        $response->assertSessionHas('message');
    }
}
