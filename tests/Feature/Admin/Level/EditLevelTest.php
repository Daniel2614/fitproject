<?php

namespace Tests\Feature\Admin\Level;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\VitalGym\Entities\User;
use App\VitalGym\Entities\Level;

class EditLevelTest extends TestCase
{
    use RefreshDatabase;

    private function validParams($overrides = [])
    {
        return array_merge([
            'name' => 'Beginner',
        ], $overrides);
    }

    /** @test */
    function an_admin_can_view_the_page_to_edit_a_level()
    {
        $this->withoutExceptionHandling();
        $adminUser = factory(User::class)->states('admin', 'active')->create();
        $plan = factory(Level::class)->create();

        $response = $this->be($adminUser)->get(route('admin.levels.edit', $plan));

        $response->assertSuccessful();
        $response->assertViewIs('admin.levels.edit');
    }

    /** @test */
    function an_admin_can_edit_a_level()
    {
        $this->withoutExceptionHandling();
        $adminUser = factory(User::class)->states('admin', 'active')->create();
        $level = factory(Level::class)->create($this->validParams());

        $response = $this->be($adminUser)->put(route('admin.levels.update', $level), $this->validParams([
            'name' => 'Expert'
        ]));

        $response->assertRedirect(route('admin.levels.index'));
        tap(Level::first(), function ( $level ) {
            $this->assertEquals('Expert', $level->name);
        });
        $response->assertSessionHas('alert-type', 'success');
        $response->assertSessionHas('message');
    }

    /** @test */
    function see_404_error_if_the_attempt_to_view_the_edit_page_with_a_level_does_not_exist()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->get(route('admin.levels.edit', '999'));

        $response->assertStatus(404);
    }
}
