<?php

namespace Tests\Feature\Admin\Level;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\VitalGym\Entities\User;
use App\VitalGym\Entities\Level;

class AddLevelTest extends TestCase
{
    use RefreshDatabase;

    private function validParams($overrides = [])
    {
        return array_merge([
            'name' => 'Beginner',
        ], $overrides);
    }

    /** @test */
    function an_admin_can_view_the_page_to_create_a_new_level()
    {
        $this->withoutExceptionHandling();
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->get(route('admin.levels.create'));

        $response->assertSuccessFul();
        $response->assertViewIs('admin.levels.create');
    }


    /** @test */
    function an_admin_can_create_a_new_level()
    {
        $this->withoutExceptionHandling();
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->post(route('admin.levels.store'), $this->validParams());

        $response->assertRedirect(route('admin.levels.index'));
        tap(Level::first(), function ( $level ) {
            $this->assertEquals('Beginner', $level->name);
        });
        $response->assertSessionHas('alert-type', 'success');
        $response->assertSessionHas('message');
    }
}
