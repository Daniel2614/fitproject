<?php

namespace Tests\Feature\Admin\Routines;

use App\VitalGym\Entities\Routine;
use App\VitalGym\Entities\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ViewRoutinesListTest extends TestCase
{
    Use RefreshDatabase;

    /** @test */
    function an_admin_can_view_routine_list()
    {
        $admin = factory(User::class)->state('admin', 'active')->create();
        factory(Routine::class)->times(15)->create();

        $response = $this->be($admin)->get(route('admin.routines.index'));

        $response->assertSuccessful();
        $response->assertViewIs('admin.routines.index');
        $expectedRoutines = Routine::with('level')->withCount('customers')->orderByDesc('created_at')->paginate();
        $response->assertViewHas('routines', $expectedRoutines);
    }
}
