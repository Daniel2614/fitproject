<?php

namespace Tests\Feature\Admin\Plan;

use App\VitalGym\Entities\User;
use Tests\TestCase;
use App\VitalGym\Entities\Plan;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ViewPlanListingTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function admin_can_view_administration_plans_list_page()
    {
        $this->withoutExceptionHandling();
        $userAdmin = factory(User::class)->states('admin', 'active')->create();
        factory(Plan::class)->times(3)->create();

        $response = $this->be($userAdmin)->get(route('admin.plans.index'));

        $response->assertSuccessful();
        $response->assertViewHas('plans');
    }

    /** @test */
    function admin_can_view_plans_list_page()
    {
        $this->withoutExceptionHandling();

        $userAdmin = factory(User::class)->states('admin', 'active')->create();

        $plans =  factory(Plan::class)->times(3)->create();

        $response = $this->be($userAdmin)->get(route('plans.index'));

        $response->assertSuccessful();
        $plans->assertEquals($response->data('plans'));
    }
}
