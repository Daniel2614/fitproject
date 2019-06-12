<?php

namespace Tests\Feature\Admin\Level;

use App\VitalGym\Entities\Level;
use App\VitalGym\Entities\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ViewLevelListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function an_admin_can_view_the_list_of_levels()
    {
        $this->withoutExceptionHandling();
        $admin = factory(User::class)->state('admin', 'active')->create();
        factory(Level::class)->times(5)->create();

        $response = $this->be($admin)->get(route('admin.levels.index'));
        $expectedLevels = Level::withCount('customers', 'routines')->orderByDesc('created_at')->paginate();

        $response->assertSuccessful();
        $response->assertViewIs('admin.levels.index');
        $response->assertViewHas('levels', $expectedLevels);
    }
}
