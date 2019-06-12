<?php

namespace Tests\Feature\Admin\Plan;

use App\VitalGym\Entities\Plan;
use App\VitalGym\Entities\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EditPlanTest extends TestCase
{
    use RefreshDatabase;

    private function validParams($overrides = [])
    {
        return array_merge([
            'name' => 'Yearly',
            'price' => 24000,
            'is_premium' => true,
        ], $overrides);
    }

    /** @test */
    function an_admin_can_view_the_page_to_edit_a_plan()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();
        $plan = factory(Plan::class)->create();

        $response = $this->be($adminUser)->get(route('admin.plans.edit', $plan));

        $response->assertSuccessful();
        $response->assertViewIs('admin.plans.edit');
        $this->assertTrue($response->data('plan')->is($plan));
    }
    
    /** @test */
    function see_404_error_if_the_attempt_to_view_the_edit_page_with_a_plan_does_not_exist()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->get(route('admin.plans.edit', '999'));

        $response->assertStatus(404);
    }
    
    /** @test */
    function an_admin_can_edit_a_plan()
    {
        $this->withoutExceptionHandling();
        $adminUser = factory(User::class)->states('admin', 'active')->create();
        $plan = factory(Plan::class)->create([
            'name' => 'Monthly',
            'name' => 4000,
            'is_premium' => false,
        ]);

        $response = $this->be($adminUser)->patch(route('admin.plans.update', $plan), $this->validParams());

        $response->assertRedirect(route('admin.plans.index'));
        tap(Plan::first(), function ( $plan ) {
           $this->assertEquals('Yearly', $plan->name);
           $this->assertEquals(24000, $plan->price);
           $this->assertTrue($plan->is_premium);
        });
        $response->assertSessionHas('alert-type', 'success');
        $response->assertSessionHas('message');
    }
    
    /** @test */
    function see_404_error_if_the_plan_does_not_exist()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->patch(route('admin.plans.update', '999'), $this->validParams());

        $response->assertStatus(404);
    }
    
    /** @test */
    function name_is_required()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();
        $plan = factory(Plan::class)->create();

        $response = $this->be($adminUser)->from(route('admin.plans.edit', $plan))->patch(route('admin.plans.update', $plan), $this->validParams([
            'name' => '',
        ]));

        $response->assertRedirect(route('admin.plans.edit', $plan));
        $response->assertSessionHasErrors('name');
    }

    /** @test */
    function name_must_have_a_maximum_of_60_characters()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();
        $plan = factory(Plan::class)->create();

        $response = $this->be($adminUser)->from(route('admin.plans.edit', $plan))->patch(route('admin.plans.update', $plan), $this->validParams([
            'name' => str_random(61),
        ]));

        $response->assertRedirect(route('admin.plans.edit', $plan));
        $response->assertSessionHasErrors('name');
    }

    /** @test */
    function price_is_required()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();
        $plan = factory(Plan::class)->create();

        $response = $this->be($adminUser)->from(route('admin.plans.edit', $plan))->patch(route('admin.plans.update', $plan), $this->validParams([
            'price' => '',
        ]));

        $response->assertRedirect(route('admin.plans.edit', $plan));
        $response->assertSessionHasErrors('price');
    }


    /** @test */
    function price_must_be_integer()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();
        $plan = factory(Plan::class)->create();

        $response = $this->be($adminUser)->from(route('admin.plans.edit', $plan))->patch(route('admin.plans.update', $plan), $this->validParams([
            'price' => '20.00',
        ]));

        $response->assertRedirect(route('admin.plans.edit', $plan));
        $response->assertSessionHasErrors('price');
    }

    /** @test */
    function is_premium_is_required()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();
        $plan = factory(Plan::class)->create();

        $response = $this->be($adminUser)->from(route('admin.plans.edit', $plan))->patch(route('admin.plans.update', $plan), $this->validParams([
            'is_premium' => '',
        ]));

        $response->assertRedirect(route('admin.plans.edit', $plan));
        $response->assertSessionHasErrors('is_premium');
    }

    /** @test */
    function is_premium_must_be_boolean()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();
        $plan = factory(Plan::class)->create();

        $response = $this->be($adminUser)->from(route('admin.plans.edit', $plan))->patch(route('admin.plans.update', $plan), $this->validParams([
            'is_premium' => 'no-boolean',
        ]));

        $response->assertRedirect(route('admin.plans.edit', $plan));
        $response->assertSessionHasErrors('is_premium');
    }
}
