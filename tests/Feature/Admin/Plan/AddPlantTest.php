<?php

namespace Tests\Feature\Admin\Plan;

use App\VitalGym\Entities\Plan;
use App\VitalGym\Entities\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AddPlantTest extends TestCase
{
    use RefreshDatabase;

    private function validParams($overrides = [])
    {
        return array_merge([
            'name' => 'Monthly',
            'price' => 3000,
            'is_premium' => false,
        ], $overrides);
    }
    
    /** @test */
    function an_admin_can_view_the_page_to_create_a_new_plan()
    {
        $this->withoutExceptionHandling();
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->get(route('admin.plans.create'));

        $response->assertSuccessFul();
        $response->assertViewIs('admin.plans.create');
    }


    /** @test */
    function an_admin_can_create_a_new_plan()
    {
        $this->withoutExceptionHandling();
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->post(route('admin.plans.store'), $this->validParams());

        $response->assertRedirect(route('admin.plans.index'));
        tap(Plan::first(), function ( $plan ) {
           $this->assertEquals('Monthly', $plan->name);
           $this->assertEquals(3000, $plan->price);
           $this->assertEquals(false, $plan->is_premium);
        });
        $response->assertSessionHas('alert-type', 'success');
        $response->assertSessionHas('message');
    }
    
    /** @test */
    function name_is_required()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->from(route('admin.plans.create'))->post(route('admin.plans.store'), $this->validParams([
            'name' => '',
        ]));

        $response->assertRedirect(route('admin.plans.create'));
        $response->assertSessionHasErrors('name');
        $this->assertEquals(0, Plan::count());
    }

    /** @test */
    function name_must_have_a_maximum_of_60_characters()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->from(route('admin.plans.create'))->post(route('admin.plans.store'), $this->validParams([
            'name' => str_random(61),
        ]));

        $response->assertRedirect(route('admin.plans.create'));
        $response->assertSessionHasErrors('name');
        $this->assertEquals(0, Plan::count());
    }

    /** @test */
    function price_is_required()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->from(route('admin.plans.create'))->post(route('admin.plans.store'), $this->validParams([
            'price' => '',
        ]));

        $response->assertRedirect(route('admin.plans.create'));
        $response->assertSessionHasErrors('price');
        $this->assertEquals(0, Plan::count());
    }

    /** @test */
    function price_must_be_an_integer()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->from(route('admin.plans.create'))->post(route('admin.plans.store'), $this->validParams([
            'price' => '20.00',
        ]));

        $response->assertRedirect(route('admin.plans.create'));
        $response->assertSessionHasErrors('price');
        $this->assertEquals(0, Plan::count());
    }

    /** @test */
    function is_premium_is_required()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->from(route('admin.plans.create'))->post(route('admin.plans.store'), $this->validParams([
            'is_premium' => '',
        ]));

        $response->assertRedirect(route('admin.plans.create'));
        $response->assertSessionHasErrors('is_premium');
        $this->assertEquals(0, Plan::count());
    }

    /** @test */
    function is_premium_must_be_boolean()
    {
        $adminUser = factory(User::class)->states('admin', 'active')->create();

        $response = $this->be($adminUser)->from(route('admin.plans.create'))->post(route('admin.plans.store'), $this->validParams([
            'is_premium' => 'invalid',
        ]));

        $response->assertRedirect(route('admin.plans.create'));
        $response->assertSessionHasErrors('is_premium');
        $this->assertEquals(0, Plan::count());
    }
}
