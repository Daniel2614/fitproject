<?php

namespace Tests\Feature\Admin\Level;

use App\VitalGym\Entities\Customer;
use App\VitalGym\Entities\Level;
use App\VitalGym\Entities\Routine;
use App\VitalGym\Entities\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeleteLevelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function an_admin_can_delete_a_level()
    {
        $this->withoutExceptionHandling();
        $admin = factory(User::class)->state('admin', 'active')->create();
        $level = factory(Level::class)->create();

        $response = $this->be($admin)->from(route('admin.levels.index'))->delete(route('admin.levels.destroy', $level));

        $response->assertRedirect(route('admin.levels.index'));
        $this->assertEquals(0, Level::count());
        $response->assertSessionHas('alert-type', 'success');
    }

    /** @test */
    function a_level_can_only_be_deleted_if_you_do_not_have_associated_customers()
    {
        $this->withoutExceptionHandling();
        $admin = factory(User::class)->state('admin', 'active')->create();
        $level = factory(Level::class)->create();
        $routine = factory(Routine::class)->create(['level_id' => $level->id]);
        factory(Customer::class)->create(['level_id' => $level->id, 'routine_id' => $routine->id]);

        $response = $this->be($admin)->from(route('admin.levels.index'))->delete(route('admin.levels.destroy', $level));

        $response->assertRedirect(route('admin.levels.index'));
        $this->assertEquals(1, Level::count());
        $response->assertSessionHas('alert-type', 'error');
    }

    /** @test */
    function a_level_can_only_be_deleted_if_you_do_not_have_associated_routiness()
    {
        $this->withoutExceptionHandling();
        $admin = factory(User::class)->state('admin', 'active')->create();
        $level = factory(Level::class)->create();
        factory(Routine::class)->create(['level_id' => $level->id]);

        $response = $this->be($admin)->from(route('admin.levels.index'))->delete(route('admin.levels.destroy', $level));

        $response->assertRedirect(route('admin.levels.index'));
        $this->assertEquals(1, Level::count());
        $response->assertSessionHas('alert-type', 'error');
    }
}
